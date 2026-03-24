export function toSlug(text: string): string {
  return text
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/(^-|-$)/g, '');
}

/**
 * Builds a combo-slug from the lizard's identifying fields.
 * Empty/unknown fields are omitted — so a lizard with no gender yet still
 * gets a valid slug, and when gender is later added the slug updates naturally.
 */
export function toLizardSlug(
  name: string,
  species: string,
  gender: string,
  dob: string,
): string {
  const parts = [name, species, gender, dob].filter(p => p.trim() !== '');
  return toSlug(parts.join(' '));
}
