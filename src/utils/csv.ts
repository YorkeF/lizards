import Papa from 'papaparse';
import type { Lizard, RawLizardRow } from '../types';
import { toLizardSlug } from './slugs';

const EXPECTED_HEADERS = [
  'Name', 'Species', 'Morph', 'Locality', 'Gender', 'Date of Birth',
  'Sire', 'Dame', 'Weight (g)', 'Price', 'Available', 'Description', 'Photo', 'Obtained From',
];

function parseAvailable(val: string): boolean {
  return ['true', 'yes', '1'].includes(val.trim().toLowerCase());
}

function parseNumber(val: string): number | null {
  const n = parseFloat(val.trim());
  return isNaN(n) ? null : n;
}

function deduplicateSlug(slug: string, seen: Map<string, number>): string {
  const count = seen.get(slug) ?? 0;
  seen.set(slug, count + 1);
  return count === 0 ? slug : `${slug}-${count + 1}`;
}

export function parseLizards(csvText: string): Lizard[] {
  const result = Papa.parse<RawLizardRow>(csvText.trim(), {
    header: true,
    skipEmptyLines: true,
  });

  if (result.data.length === 0) return [];

  // Warn if unexpected headers
  const actualHeaders = result.meta.fields ?? [];
  const missing = EXPECTED_HEADERS.filter(h => !actualHeaders.includes(h));
  if (missing.length > 0) {
    console.warn('[lizards] CSV missing expected columns:', missing);
  }

  const slugsSeen = new Map<string, number>();

  return result.data.map((row): Lizard => {
    const name = (row.Name ?? '').trim();
    const species = (row.Species ?? '').trim();
    const gender = (row.Gender ?? '').trim();
    const dateOfBirth = (row['Date of Birth'] ?? '').trim();
    const rawSlug = toLizardSlug(name, species, gender, dateOfBirth);
    const slug = deduplicateSlug(rawSlug, slugsSeen);
    return {
      slug,
      name,
      species,
      morph: (row.Morph ?? '').trim(),
      locality: (row.Locality ?? '').trim(),
      gender,
      dateOfBirth,
      sire: (row.Sire ?? '').trim(),
      dame: (row.Dame ?? '').trim(),
      weightG: parseNumber(row['Weight (g)'] ?? ''),
      price: parseNumber(row.Price ?? ''),
      available: parseAvailable(row.Available ?? ''),
      description: (row.Description ?? '').trim(),
      photos: (row.Photo ?? '').trim() ? [(row.Photo ?? '').trim()] : [],
      obtainedFrom: (row['Obtained From'] ?? '').trim(),
    };
  });
}

export function unparseLizards(lizards: Lizard[]): string {
  const rows: RawLizardRow[] = lizards.map(l => ({
    Name: l.name,
    Species: l.species,
    Morph: l.morph,
    Locality: l.locality,
    Gender: l.gender,
    'Date of Birth': l.dateOfBirth,
    Sire: l.sire,
    Dame: l.dame,
    'Weight (g)': l.weightG !== null ? String(l.weightG) : '',
    Price: l.price !== null ? String(l.price) : '',
    Available: l.available ? 'Yes' : 'No',
    Description: l.description,
    Photo: l.photos[0] ?? '',
    'Obtained From': l.obtainedFrom,
  }));

  return Papa.unparse(rows, { columns: EXPECTED_HEADERS });
}

export async function fetchLizards(): Promise<Lizard[]> {
  const res = await fetch('/api/lizards.php');
  if (!res.ok) throw new Error(`Failed to fetch lizards: ${res.status}`);
  const rows: Array<Record<string, string | number | null>> = await res.json();

  const slugsSeen = new Map<string, number>();
  return rows.map((row): Lizard => {
    const name = String(row.name ?? '');
    const species = String(row.species ?? '');
    const gender = String(row.gender ?? '');
    const dateOfBirth = String(row.date_of_birth ?? '');
    const slug = deduplicateSlug(toLizardSlug(name, species, gender, dateOfBirth), slugsSeen);
    return {
      slug,
      name,
      species,
      morph: String(row.morph ?? ''),
      locality: String(row.locality ?? ''),
      gender,
      dateOfBirth,
      sire: String(row.sire ?? ''),
      dame: String(row.dame ?? ''),
      weightG: row.weight_g !== null ? Number(row.weight_g) : null,
      price: row.price !== null ? Number(row.price) : null,
      available: Boolean(row.available),
      description: String(row.description ?? ''),
      photos: Array.isArray(row.photos) ? (row.photos as string[]) : [],
      obtainedFrom: String(row.obtained_from ?? ''),
    };
  });
}
