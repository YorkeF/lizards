import styles from './ProductCard.module.css';

interface Props {
  name: string;
  subtitle?: string;
  price: number | null;
  available: boolean;
  photos: string[];
}

export function ProductCard({ name, subtitle, price, available, photos }: Props) {
  const base = import.meta.env.BASE_URL;
  const imgSrc = photos.length > 0
    ? `${base}images/${photos[0]}`
    : `${base}images/placeholder.svg`;

  return (
    <div className={styles.card}>
      <div className={styles.imageWrap}>
        <img
          src={imgSrc}
          alt={name}
          onError={e => { (e.target as HTMLImageElement).src = `${base}images/placeholder.svg`; }}
        />
      </div>
      <div className={styles.body}>
        <h3 className={styles.name}>{name}</h3>
        {subtitle && <p className={styles.sub}>{subtitle}</p>}
        <div className={styles.footer}>
          <span className={available ? styles.available : styles.unavailable}>
            {available ? 'Available' : 'Not Available'}
          </span>
          <span className={styles.price}>
            {price !== null ? `$${price}` : 'Contact for price'}
          </span>
        </div>
      </div>
    </div>
  );
}
