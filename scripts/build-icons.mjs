import fs from 'node:fs';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { far } from '@fortawesome/free-regular-svg-icons';
import { fab } from '@fortawesome/free-brands-svg-icons';
const files = fs.readdirSync('resources/views', { recursive: true }).filter(p => p.endsWith('.blade.php'));
const text = files.map(p => fs.readFileSync(`resources/views/${p}`, 'utf8')).join('\n');
const names = new Set([...text.matchAll(/fa-([a-z0-9-]+)/g)].map(m => m[1]));
const icons = {};
for (const [family, set] of Object.entries({ solid: fas, regular: far, brands: fab })) {
 for (const icon of Object.values(set)) {
  const [width, height, aliases, , paths] = icon.icon;
  for (const name of [icon.iconName, ...aliases.filter(a => typeof a === 'string')]) {
   if (names.has(name)) icons[`${family}:${name}`] = { width, height, paths: Array.isArray(paths) ? paths : [paths] };
  }
 }
}
// Names previously used but not supplied by the free icon set.
for (const [name, replacement] of Object.entries({'wifi-slash':'wifi','arrow-down-left':'arrow-down','arrow-up-right':'arrow-up','truck-delete':'truck'})) {
 const icon = Object.values(fas).find(i => i.iconName === replacement);
 const [width,height,,,path] = icon.icon;
 icons[`solid:${name}`] = {width,height,paths:[path]};
}
fs.writeFileSync('resources/icons/fontawesome.json', JSON.stringify(icons));
