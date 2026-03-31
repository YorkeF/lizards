import { usePlants } from '../../hooks/usePlants';
import { ProductCard } from '../../components/ProductCard/ProductCard';
import styles from './Plants.module.css';

export function Plants() {
  const { plants, loading, error } = usePlants();

  if (loading) return <div className={styles.state}>Loading...</div>;
  if (error) return <div className={styles.state}>Error loading plants: {error}</div>;

  return (
    <main className={styles.main}>
      <h1 className={styles.title}>Plants</h1>
      <p className={styles.subtitle}>Live plants for naturalistic enclosures.</p>
      {plants.length === 0 ? (
        <div className={styles.empty}>
          <p>No plants listed yet. Check back soon!</p>
        </div>
      ) : (
        <div className={styles.grid}>
          {plants.map(plant => (
            <ProductCard
              key={plant.id}
              name={plant.name}
              subtitle={plant.scientificName || undefined}
              price={plant.price}
              available={plant.available}
              photos={plant.photos}
            />
          ))}
        </div>
      )}
    </main>
  );
}
