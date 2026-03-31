import { useIsopods } from '../../hooks/useIsopods';
import { ProductCard } from '../../components/ProductCard/ProductCard';
import styles from './Isopods.module.css';

export function Isopods() {
  const { isopods, loading, error } = useIsopods();

  if (loading) return <div className={styles.state}>Loading...</div>;
  if (error) return <div className={styles.state}>Error loading isopods: {error}</div>;

  return (
    <main className={styles.main}>
      <h1 className={styles.title}>Isopods</h1>
      <p className={styles.subtitle}>Bioactive cleanup crew and feeders.</p>
      {isopods.length === 0 ? (
        <div className={styles.empty}>
          <p>No isopods listed yet. Check back soon!</p>
        </div>
      ) : (
        <div className={styles.grid}>
          {isopods.map(iso => (
            <ProductCard
              key={iso.id}
              name={iso.name}
              subtitle={iso.scientificName || undefined}
              price={iso.price}
              available={iso.available}
              photos={iso.photos}
            />
          ))}
        </div>
      )}
    </main>
  );
}
