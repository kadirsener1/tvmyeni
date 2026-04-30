<?php include "auth.php"; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Admin Paneli - Kanal Yönetimi</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { font-family: sans-serif; padding: 20px; background: #f9f9f9; }
    input, select, button { padding: 8px; margin: 5px 0; width: 100%; max-width: 400px; display: block; }
    table { border-collapse: collapse; width: 100%; max-width: 800px; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
    th { background: #eee; }
    button.delete { background: red; color: white; border: none; padding: 6px 12px; cursor: pointer; }
    .filters { margin-top: 20px; }
  </style>
</head>
<!-- (head ve stil bölümü önceki gibi aynı kalabilir) -->

<body>
 <a href="logout.php">Çıkış Yap</a>
 <h2>📺 Admin Paneli: Kanal Yönetimi</h2>

  <!-- Kategori Filtreleme -->
  <label for="filter">Kategori Filtresi:</label>
  <select id="filter" onchange="renderChannels()">
    <option value="all">Tümü</option>
  </select>

  <!-- Kanal Ekleme / Güncelleme Formu -->
  <form id="addChannelForm">
    <input type="hidden" id="editIndex" value="-1">
    <input type="text" id="title" placeholder="Kanal Adı" required>
    <input type="url" id="url" placeholder="Video URL" required>
    <select id="type" required>
      <option value="">Tür Seç</option>
      <option value="hls">HLS</option>
      <option value="iframe">Iframe</option>
      <option value="mp4">MP4</option>
      <option value="youtube">YouTube</option>
      <option value="vimeo">Vimeo</option>
      
    </select>
    <input type="text" id="category" placeholder="Kategori" required>
    <button type="submit" id="submitButton">Kanal Ekle</button>
  </form>

  <!-- Kanal Tablosu -->
  <table id="channelTable">
    <thead>
      <tr>
        <th>Ad</th><th>Tür</th><th>Kategori</th><th>İşlem</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>

  <script>
    let allChannels = [];

    async function loadChannels() {
      const res = await fetch('channels.json');
      allChannels = await res.json();
      populateFilterOptions();
      renderChannels();
    }

    function populateFilterOptions() {
      const filter = document.getElementById('filter');
      const categories = [...new Set(allChannels.map(c => c.category))];
      filter.innerHTML = '<option value="all">Tümü</option>';
      categories.forEach(cat => {
        const opt = document.createElement('option');
        opt.value = cat;
        opt.textContent = cat;
        filter.appendChild(opt);
      });
    }

    function renderChannels() {
      const filterVal = document.getElementById('filter').value;
      const tbody = document.querySelector('#channelTable tbody');
      tbody.innerHTML = '';
      allChannels.forEach((c, i) => {
        if (filterVal !== 'all' && c.category !== filterVal) return;
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${c.title}</td>
          <td>${c.type}</td>
          <td>${c.category}</td>
          <td>
            <button onclick="editChannel(${i})">Düzenle</button>
            <button class="delete" onclick="deleteChannel(${i})">Sil</button>
            <button onclick="moveUp(${i})">↑</button>
            <button onclick="moveDown(${i})">↓</button>
          </td>
        `;
        tbody.appendChild(row);
      });
    }

    function editChannel(index) {
      const channel = allChannels[index];
      document.getElementById('title').value = channel.title;
      document.getElementById('url').value = channel.url;
      document.getElementById('type').value = channel.type;
      document.getElementById('category').value = channel.category;
      document.getElementById('editIndex').value = index;
      document.getElementById('submitButton').textContent = "Güncelle";
    }

    async function deleteChannel(index) {
      allChannels.splice(index, 1);
      await saveChannels(allChannels);
    }

    function moveUp(index) {
      if (index > 0) {
        [allChannels[index], allChannels[index - 1]] = [allChannels[index - 1], allChannels[index]];
        saveChannels(allChannels);
      }
    }

    function moveDown(index) {
      if (index < allChannels.length - 1) {
        [allChannels[index], allChannels[index + 1]] = [allChannels[index + 1], allChannels[index]];
        saveChannels(allChannels);
      }
    }

    async function saveChannels(data) {
      await fetch('save.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      resetForm();
      loadChannels();
    }

    function resetForm() {
      document.getElementById('addChannelForm').reset();
      document.getElementById('editIndex').value = -1;
      document.getElementById('submitButton').textContent = "Kanal Ekle";
    }

    document.getElementById('addChannelForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const title = document.getElementById('title').value.trim();
      const url = document.getElementById('url').value.trim();
      const type = document.getElementById('type').value;
      const category = document.getElementById('category').value.trim();
      const index = parseInt(document.getElementById('editIndex').value);

      if (index === -1) {
        allChannels.push({ title, url, type, category });
      } else {
        allChannels[index] = { title, url, type, category };
      }

      await saveChannels(allChannels);
    });

    loadChannels();
  </script>
</body>
</html>
