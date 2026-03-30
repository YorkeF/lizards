import { useEffect } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import { useLizards } from '../../hooks/useLizards';
import { fetchSlugHistory } from '../../utils/slugHistory';
import { Slideshow } from '../../components/Slideshow/Slideshow';
import styles from './LizardProfile.module.css';

function age(dob: string): string {
  if (!dob) return 'Unknown';
  const birth = new Date(dob);
  if (isNaN(birth.getTime())) return 'Unknown';
  const now = new Date();
  const months =
    (now.getFullYear() - birth.getFullYear()) * 12 +
    (now.getMonth() - birth.getMonth());
  if (months < 1) return 'Less than 1 month';
  if (months < 24) return `${months} month${months !== 1 ? 's' : ''}`;
  const years = Math.floor(months / 12);
  return `${years} year${years !== 1 ? 's' : ''}`;
}

export function LizardProfile() {
  const { slug } = useParams<{ slug: string }>();
  const { lizards, loading, error } = useLizards();
  const navigate = useNavigate();

  const lizard = lizards.find(l => l.slug === slug);

  // If slug not found after data loads, check slug-history for a redirect
  useEffect(() => {
    if (loading || lizard || !slug) return;
    fetchSlugHistory().then(history => {
      const name = history[slug];
      if (!name) return;
      const current = lizards.find(l => l.name === name);
      if (current) navigate(`/lizard/${current.slug}`, { replace: true });
    });
  }, [loading, lizard, slug, lizards, navigate]);

  if (loading) return <div className={styles.state}>Loading...</div>;
  if (error) return <div className={styles.state}>Error: {error}</div>;

  if (!lizard) {
    return (
      <div className={styles.state}>
        <h2>Lizard not found</h2>
        <p><Link to="/">Back to store</Link></p>
      </div>
    );
  }

  const sireRecord = lizard.sire
    ? lizards.find(l => l.name.toLowerCase() === lizard.sire.toLowerCase())
    : null;
  const dameRecord = lizard.dame
    ? lizards.find(l => l.name.toLowerCase() === lizard.dame.toLowerCase())
    : null;

  const children = lizards.filter(
    l =>
      l.sire.toLowerCase() === lizard.name.toLowerCase() ||
      l.dame.toLowerCase() === lizard.name.toLowerCase(),
  );

  return (
    <main className={styles.main}>
      <Link to="/" className={styles.back}>← Back to store</Link>

      <div className={styles.hero}>
        <Slideshow photos={lizard.photos} alt={lizard.name} />
        <div className={styles.header}>
          <h1>{lizard.name}</h1>
          <p className={styles.sub}>
            {lizard.species}{lizard.morph ? ` · ${lizard.morph}` : ''}
          </p>
          <span className={lizard.available ? styles.available : styles.unavailable}>
            {lizard.available ? 'Available' : 'Not Available'}
          </span>
          {lizard.price !== null && (
            <p className={styles.price}>${lizard.price}</p>
          )}
        </div>
      </div>

      <div className={styles.grid}>
        <section className={styles.section}>
          <h2>Details</h2>
          <dl className={styles.dl}>
            <dt>Gender</dt>
            <dd>{lizard.gender || '—'}</dd>
            <dt>Date of Birth</dt>
            <dd>{lizard.dateOfBirth || '—'}</dd>
            <dt>Age</dt>
            <dd>{age(lizard.dateOfBirth)}</dd>
            <dt>Weight</dt>
            <dd>{lizard.weightG !== null ? `${lizard.weightG}g` : '—'}</dd>
            {lizard.locality && <><dt>Locality</dt><dd>{lizard.locality}</dd></>}
            {lizard.obtainedFrom && <><dt>Obtained From</dt><dd>{lizard.obtainedFrom}</dd></>}
          </dl>
        </section>

        <section className={styles.section}>
          <h2>Lineage</h2>
          <dl className={styles.dl}>
            <dt>Sire (Father)</dt>
            <dd>
              {lizard.sire
                ? sireRecord
                  ? <Link to={`/lizard/${sireRecord.slug}`}>{lizard.sire}</Link>
                  : <span>{lizard.sire} <span className={styles.ghost}>(not in records)</span></span>
                : '—'}
            </dd>
            <dt>Dame (Mother)</dt>
            <dd>
              {lizard.dame
                ? dameRecord
                  ? <Link to={`/lizard/${dameRecord.slug}`}>{lizard.dame}</Link>
                  : <span>{lizard.dame} <span className={styles.ghost}>(not in records)</span></span>
                : '—'}
            </dd>
          </dl>
        </section>

        {children.length > 0 && (
          <section className={`${styles.section} ${styles.wide}`}>
            <h2>Offspring</h2>
            <ul className={styles.offspringList}>
              {children.map(c => (
                <li key={c.slug}>
                  <Link to={`/lizard/${c.slug}`}>{c.name}</Link>
                  {c.species ? ` — ${c.species}` : ''}
                </li>
              ))}
            </ul>
          </section>
        )}

        {lizard.description && (
          <section className={`${styles.section} ${styles.wide}`}>
            <h2>About</h2>
            <p>{lizard.description}</p>
          </section>
        )}
      </div>
    </main>
  );
}
