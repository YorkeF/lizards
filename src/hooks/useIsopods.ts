import { useEffect, useState } from 'react';
import type { Isopod } from '../types';

interface UseIsopodsResult {
  isopods: Isopod[];
  loading: boolean;
  error: string | null;
}

export function useIsopods(): UseIsopodsResult {
  const [isopods, setIsopods] = useState<Isopod[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    fetch('/api/isopods.php')
      .then(r => {
        if (!r.ok) throw new Error(`HTTP ${r.status}`);
        return r.json();
      })
      .then((data: Array<{
        id: number;
        name: string;
        scientific_name: string;
        description: string;
        price: number | null;
        available: number;
        obtained_from: string;
        photos: string;
      }>) => {
        setIsopods(data.map(d => ({
          id: d.id,
          name: d.name,
          scientificName: d.scientific_name ?? '',
          description: d.description ?? '',
          price: d.price,
          available: d.available === 1,
          obtainedFrom: d.obtained_from ?? '',
          photos: d.photos ? d.photos.split(',') : [],
        })));
        setLoading(false);
      })
      .catch(err => {
        setError(String(err));
        setLoading(false);
      });
  }, []);

  return { isopods, loading, error };
}
