import { useEffect, useState } from 'react';
import { fetchLizards } from '../utils/csv';
import type { Lizard } from '../types';

interface UseLizardsResult {
  lizards: Lizard[];
  loading: boolean;
  error: string | null;
}

export function useLizards(): UseLizardsResult {
  const [lizards, setLizards] = useState<Lizard[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    fetchLizards()
      .then(data => {
        setLizards(data);
        setLoading(false);
      })
      .catch(err => {
        setError(String(err));
        setLoading(false);
      });
  }, []);

  return { lizards, loading, error };
}
