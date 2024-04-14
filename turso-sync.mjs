import { createClient } from "@libsql/client";

let databaseURL = process.argv.slice(2)[0];
const accessToken = process.argv.slice(2)[1];
const replicaPath = process.argv.slice(2)[2];

if (databaseURL.startsWith('https://')) {
  databaseURL = databaseURL.replace('https://', 'libsql://');
}

const client = createClient({
  url: `file:${replicaPath}`,
  syncUrl: databaseURL,
  authToken: accessToken,
});

console.log('Syncing database to replica ' + replicaPath + ' from ' + databaseURL);

await client.sync();

console.log('Sync completed.');
