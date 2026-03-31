import { useLizards } from '../../hooks/useLizards';
import { LizardCard } from '../../components/LizardCard/LizardCard';
import styles from './Breeders.module.css';

export function Breeders() {
  const { lizards, loading, error } = useLizards();

  if (loading) return <div className={styles.state}>Loading...</div>;
  if (error) return <div className={styles.state}>Error loading lizards: {error}</div>;

  const breeders = lizards.filter(l => l.isBreeder);

  return (
    <main className={styles.main}>
      <h1 className={styles.title}>Our Breeders</h1>
      <p className={styles.subtitle}>These animals are part of our breeding program and are not for sale.</p>
      {breeders.length === 0 ? (
        <div className={styles.empty}>
          <p>No breeders listed yet. Check back soon!</p>
        </div>
      ) : (
        <div className={styles.grid}>
          {breeders.map(l => (
            <LizardCard key={l.slug} lizard={l} hideFooter />
          ))}
        </div>
      )}
    </main>
  );
}
