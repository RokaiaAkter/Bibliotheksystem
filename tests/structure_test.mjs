import { readFile, stat } from "node:fs/promises";
import { resolve } from "node:path";

const projectRoot = resolve(import.meta.dirname, "..");
const requiredFiles = [
    "public/index.php",
    "app/Core/Auth.php",
    "app/Core/Csrf.php",
    "app/Controllers/BookController.php",
    "app/Services/LoanService.php",
    "app/Repositories/BookRepository.php",
    "database/init/001_schema.sql",
    "public/assets/js/app.js",
    "docs/TROUBLESHOOTING.md",
];

for (const relativePath of requiredFiles) {
    const fileStat = await stat(resolve(projectRoot, relativePath));

    if (!fileStat.isFile() || fileStat.size === 0) {
        throw new Error(`Missing or empty file: ${relativePath}`);
    }
}

const router = await readFile(resolve(projectRoot, "public/index.php"), "utf8");
const requiredRoutes = [
    "books/create",
    "books/import",
    "loans/checkout",
    "users/create",
    "phones/create",
    "tickets/create",
    "system",
    "api/books",
];

for (const route of requiredRoutes) {
    if (!router.includes(`'${route}'`)) {
        throw new Error(`Route is not registered: ${route}`);
    }
}

const auth = await readFile(resolve(projectRoot, "app/Core/Auth.php"), "utf8");
for (const role of ["admin", "librarian", "support"]) {
    if (!auth.includes(`'${role}'`)) {
        throw new Error(`Missing role: ${role}`);
    }
}

const securitySources = await Promise.all([
    readFile(resolve(projectRoot, "app/Core/Csrf.php"), "utf8"),
    readFile(resolve(projectRoot, "app/Repositories/BookRepository.php"), "utf8"),
    readFile(resolve(projectRoot, "app/Controllers/BookController.php"), "utf8"),
]);
const securityText = securitySources.join("\n");

for (const marker of ["hash_equals", "prepare(", "LIBXML_NONET", "<!DOCTYPE"]) {
    if (!securityText.includes(marker)) {
        throw new Error(`Missing security marker: ${marker}`);
    }
}

console.log("Structure and security marker tests passed.");
