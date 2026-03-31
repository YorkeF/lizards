import { Link } from 'react-router-dom';
import { useLizards } from '../../hooks/useLizards';
import { LizardCard } from '../../components/LizardCard/LizardCard';
import styles from './Home.module.css';

export function Home() {
  const { lizards, loading, error } = useLizards();

  if (loading) return <div className={styles.state}>Loading...</div>;
  if (error) return <div className={styles.state}>Error loading lizards: {error}</div>;

  const forSale = lizards.filter(l => !l.isBreeder);

  return (
    <main className={styles.main}>
      <div className={styles.hero}>
        <img src="/images/logo-badge.png" alt="Lichen Leachies" className={styles.heroBadge} />
        <p className={styles.tagline}>Quality leachianus &amp; chahouas</p>
      </div>
      <h1 className={styles.title}>Available Lizards</h1>
      {forSale.length === 0 ? (
        <div className={styles.empty}>
          <p>Nothing for sale right now.</p>
          <p>Check out our <Link to="/breeders">breeder lizards</Link>!</p>
        </div>
      ) : (
        <div className={styles.grid}>
          {forSale.map(l => (
            <LizardCard key={l.slug} lizard={l} />
          ))}
        </div>
      )}
    </main>
  );
}
