import { Link } from 'react-router-dom';
import type { Lizard } from '../../types';
import styles from './LizardCard.module.css';

interface Props {
  lizard: Lizard;
  hideFooter?: boolean;
}

export function LizardCard({ lizard, hideFooter }: Props) {
  const base = import.meta.env.BASE_URL;
  const imgSrc = lizard.photos.length > 0
    ? `${base}images/${lizard.photos[0]}`
    : `${base}images/placeholder.svg`;

  return (
    <Link to={`/lizard/${lizard.slug}`} className={styles.card}>
      <div className={styles.imageWrap}>
        <img
          src={imgSrc}
          alt={lizard.name}
          onError={e => { (e.target as HTMLImageElement).src = `${base}images/placeholder.svg`; }}
        />
      </div>
      <div className={styles.body}>
        <h3 className={styles.name}>{lizard.name}</h3>
        <p className={styles.sub}>{lizard.species}{lizard.morph ? ` · ${lizard.morph}` : ''}</p>
        {!hideFooter && (
          <div className={styles.footer}>
            <span className={lizard.available ? styles.available : styles.unavailable}>
              {lizard.available ? 'Available' : 'Not Available'}
            </span>
            <span className={styles.price}>
              {lizard.price !== null ? `$${lizard.price}` : 'Contact for price'}
            </span>
          </div>
        )}
      </div>
    </Link>
  );
}
