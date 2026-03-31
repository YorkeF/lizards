import { NavLink, Link } from 'react-router-dom';
import styles from './NavBar.module.css';

export function NavBar() {
  return (
    <nav className={styles.nav}>
      <Link to="/" className={styles.brand}>
        <img src="/images/logo-hero.png" alt="Lichen Leachies" className={styles.logo} />
      </Link>
      <div className={styles.links}>
        <NavLink to="/" end className={({ isActive }) => isActive ? styles.active : ''}>
          Store
        </NavLink>
        <NavLink to="/breeders" className={({ isActive }) => isActive ? styles.active : ''}>
          Breeders
        </NavLink>
        <NavLink to="/isopods" className={({ isActive }) => isActive ? styles.active : ''}>
          Isopods
        </NavLink>
        <NavLink to="/plants" className={({ isActive }) => isActive ? styles.active : ''}>
          Plants
        </NavLink>
        <NavLink to="/family-tree" className={({ isActive }) => isActive ? styles.active : ''}>
          Family Tree
        </NavLink>
      </div>
    </nav>
  );
}
