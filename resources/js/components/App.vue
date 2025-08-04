<template>
  <div class="app-wrapper">
    <header>
      <h1>📋 Company Asset Inventory</h1>
      <p>Track and export your department's assets with ease.</p>
    </header>

    <section class="asset-grid">
      <div v-for="(asset, index) in assets" :key="index" class="asset-card">
        <h2>{{ asset.name }}</h2>
        <p><strong>Department:</strong> {{ asset.department }}</p>
      </div>
    </section>

    <footer>
      <button @click="downloadCSV">⬇️ Download CSV</button>
    </footer>
  </div>
</template>

<script setup>
const assets = [
  { name: "Printer", department: "HR" },
  { name: "Monitor", department: "IT" },
  { name: "Barcode Scanner", department: "ACCOUNT" },
]

function downloadCSV() {
  const header = "Asset Name,Department\n"
  const rows = assets.map(asset => `${asset.name},${asset.department}`).join("\n")
  const blob = new Blob([header + rows], { type: 'text/csv' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = 'assets.csv'
  a.click()
  URL.revokeObjectURL(url)
}
</script>

<style scoped>
/* Layout */
.app-wrapper {
  max-width: 900px;
  margin: 0 auto;
  padding: 2rem;
  font-family: "Segoe UI", sans-serif;
  background: linear-gradient(to right, #f4f9ff, #e7f2ff);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

/* Header */
header {
  text-align: center;
}
header h1 {
  font-size: 2rem;
  color: #1e3a8a;
  margin-bottom: 0.5rem;
}
header p {
  color: #555;
  font-size: 1rem;
}

/* Cards */
.asset-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
  gap: 1rem;
}
.asset-card {
  background-color: #ffffff;
  border: 1px solid #dbeafe;
  border-radius: 12px;
  padding: 1rem;
  box-shadow: 0 4px 6px rgba(0,0,0,0.05);
  transition: transform 0.2s;
}
.asset-card:hover {
  transform: translateY(-5px);
}
.asset-card h2 {
  margin: 0;
  font-size: 1.25rem;
  color: #111827;
}
.asset-card p {
  margin-top: 0.5rem;
  font-size: 0.95rem;
  color: #374151;
}

/* Footer */
footer {
  text-align: center;
}
button {
  padding: 10px 18px;
  font-size: 1rem;
  background-color: #2563eb;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.2s;
}
button:hover {
  background-color: #1d4ed8;
}
</style>
