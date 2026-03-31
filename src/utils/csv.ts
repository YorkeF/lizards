import type { Lizard, WeightEntry } from '../types';
import { toLizardSlug } from './slugs';

function deduplicateSlug(slug: string, seen: Map<string, number>): string {
  const count = seen.get(slug) ?? 0;
  seen.set(slug, count + 1);
  return count === 0 ? slug : `${slug}-${count + 1}`;
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
      weightHistory: Array.isArray(row.weight_history)
        ? (row.weight_history as Array<{ date: string; weight_g: number }>).map(
            (e): WeightEntry => ({ date: e.date, weightG: e.weight_g }),
          )
        : [],
    };
  });
}
