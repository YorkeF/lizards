import { HashRouter, Routes, Route } from 'react-router-dom';
import { NavBar } from './components/NavBar/NavBar';
import { Home } from './pages/Home/Home';
import { Breeders } from './pages/Breeders/Breeders';
import { LizardProfile } from './pages/LizardProfile/LizardProfile';
import { FamilyTree } from './pages/FamilyTree/FamilyTree';
import { Isopods } from './pages/Isopods/Isopods';
import { Plants } from './pages/Plants/Plants';

function App() {
  return (
    <HashRouter>
      <NavBar />
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/breeders" element={<Breeders />} />
        <Route path="/lizard/:slug" element={<LizardProfile />} />
        <Route path="/family-tree" element={<FamilyTree />} />
        <Route path="/isopods" element={<Isopods />} />
        <Route path="/plants" element={<Plants />} />
      </Routes>
    </HashRouter>
  );
}

export default App;
