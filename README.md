# Lizard Store

A website for listing and selling lizards, with genealogy tracking. Hosted at `lizards.yorkeferrell.com`.

---

## Managing Lizard Records

Lizard data is stored in `public/data/lizards.csv`. Edit this file directly to add, update, or remove lizards, then push to GitHub to deploy the changes.

### CSV Columns

```
Name, Species, Morph, Locality, Gender, Date of Birth, Sire, Dame, Weight (g), Price, Available, Description, Photo, Obtained From
```

- All fields are optional except **Name**
- **Sire** and **Dame** reference other lizards by their **Name** — spelling must match exactly
- **Available** — use `Yes` or `No`
- **Price** — leave blank to show "Contact for price" on the site
- **Gender** — can be left blank if unknown and filled in later
- **Locality** — geographic/lineage variant (e.g. Nuu Ana, Moro, Pine Island)
- **Obtained From** — breeder or source the lizard came from
- **Photo** — path relative to the `public/images/` folder (e.g. `leo.jpg` or `crested-geckos/leo.jpg`)

### Adding Photos

Place photo files in `public/images/`. Subfolders are supported for organisation (e.g. `public/images/crested-geckos/`). Supported formats: `.jpg`, `.jpeg`, `.png`, `.webp`.

### Deploying Changes

After editing the CSV or adding photos:

```bash
git add .
git commit -m "Update lizard records"
git push
```

GitHub Pages redeploys automatically within a minute or two.

### Slug History

If a lizard's **Name**, **Species**, **Gender**, or **Date of Birth** is updated, their URL will change. The file `public/data/slug-history.json` maps old URLs to the current lizard name so that existing links continue to redirect correctly. Update this file manually if needed, using the format:

```json
{
  "old-slug-here": "Lizard Name"
}
```

---

## Developer Notes

### Running Locally

```bash
npm install
npm run dev
```

Opens at `http://localhost:5173/lizards/`

### Building for Deployment

```bash
npm run build
```

Output goes to `/dist/`. Push to `main` branch — GitHub Pages deploys automatically.

### Project Structure

```
public/
  data/
    lizards.csv          ← lizard records
    slug-history.json    ← keeps old URLs redirecting correctly
  images/                ← lizard photos (subfolders allowed)
src/
  utils/csv.ts           ← CSV parsing logic
  utils/slugs.ts         ← URL slug generation (combo of name/species/gender/dob)
  utils/genealogy.ts     ← family tree builder
  utils/slugHistory.ts   ← old slug redirect lookup
  pages/Home             ← store listing
  pages/LizardProfile    ← individual lizard page
  pages/FamilyTree       ← genealogy tree
```
