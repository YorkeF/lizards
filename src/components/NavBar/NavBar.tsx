import { NavLink, Link } from 'react-router-dom';
import styles from './NavBar.module.css';

export function NavBar() {
  return (
    <nav className={styles.nav}>
      <Link to="/" className={styles.brand}>Lichen Leachies</Link>
      <div className={styles.links}>
        <NavLink to="/" end className={({ isActive }) => isActive ? styles.active : ''}>
          Store
        </NavLink>
        <NavLink to="/family-tree" className={({ isActive }) => isActive ? styles.active : ''}>
          Family Tree
        </NavLink>
      </div>
    </nav>
  );
}
