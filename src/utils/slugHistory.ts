/** Maps old slugs → lizard name (the stable identifier). */
export type SlugHistory = Record<string, string>;

export async function fetchSlugHistory(): Promise<SlugHistory> {
  const base = import.meta.env.BASE_URL;
  try {
    const res = await fetch(`${base}data/slug-history.json`);
    if (!res.ok) return {};
    return (await res.json()) as SlugHistory;
  } catch {
    return {};
  }
}

