import { HashRouter, Routes, Route } from 'react-router-dom';
import { NavBar } from './components/NavBar/NavBar';
import { Home } from './pages/Home/Home';
import { Breeders } from './pages/Breeders/Breeders';
import { LizardProfile } from './pages/LizardProfile/LizardProfile';
import { FamilyTree } from './pages/FamilyTree/FamilyTree';

function App() {
  return (
    <HashRouter>
      <NavBar />
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/breeders" element={<Breeders />} />
        <Route path="/lizard/:slug" element={<LizardProfile />} />
        <Route path="/family-tree" element={<FamilyTree />} />
      </Routes>
    </HashRouter>
  );
}

export default App;
