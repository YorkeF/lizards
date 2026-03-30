import { useState } from 'react';
import styles from './Slideshow.module.css';

interface Props {
  photos: string[];
  alt: string;
}

export function Slideshow({ photos, alt }: Props) {
  const [index, setIndex] = useState(0);
  const base = import.meta.env.BASE_URL;

  const src = photos.length > 0
    ? `${base}images/${photos[index]}`
    : `${base}images/placeholder.svg`;

  const prev = () => setIndex(i => (i - 1 + photos.length) % photos.length);
  const next = () => setIndex(i => (i + 1) % photos.length);

  return (
    <div className={styles.slideshow}>
      <div className={styles.imageWrap}>
        <img
          src={src}
          alt={alt}
          onError={e => { (e.target as HTMLImageElement).src = `${base}images/placeholder.svg`; }}
        />
        {photos.length > 1 && (
          <>
            <button className={`${styles.arrow} ${styles.prev}`} onClick={prev} aria-label="Previous">‹</button>
            <button className={`${styles.arrow} ${styles.next}`} onClick={next} aria-label="Next">›</button>
          </>
        )}
      </div>
      {photos.length > 1 && (
        <div className={styles.dots}>
          {photos.map((_, i) => (
            <button
              key={i}
              className={`${styles.dot} ${i === index ? styles.active : ''}`}
              onClick={() => setIndex(i)}
              aria-label={`Photo ${i + 1}`}
            />
          ))}
        </div>
      )}
    </div>
  );
}
