<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Entities</title>
<style>
  table { border-collapse: collapse; }
  th, td { border: 1px solid #333; padding: 4px; }
  label { margin-right: 8px; }
</style>
</head>
<body>
<h1>Entities</h1>
<div>
  <form id="createForm">
    <label>Name <input type="text" name="name" required maxlength="100"></label>
    <label>SKU <input type="text" name="sku" required maxlength="50"></label>
    <label>Price <input type="number" name="price" min="0" step="0.01" value="0"></label>
    <label>Quantity <input type="number" name="quantity" min="0" step="1" value="0"></label>
    <label>Note <input type="text" name="note" maxlength="1000"></label>
    <button type="submit">Add</button>
  </form>
</div>
<hr>
<div>
  <table id="list">
    <thead><tr><th>ID</th><th>Name</th><th>SKU</th><th>Price</th><th>Quantity</th><th>Note</th><th>Actions</th></tr></thead>
    <tbody></tbody>
  </table>
</div>

<script nonce="<?=htmlspecialchars($nonce ?? '', ENT_QUOTES)?>">
(function(){
  var api = '/entities';
  var tbody = document.querySelector('#list tbody');
  function row(item){
    var tr = document.createElement('tr');
    tr.innerHTML = '<td>'+item.id+'</td>'+
      '<td><input data-k="name" value="'+escapeHtml(item.name)+'"></td>'+
      '<td><input data-k="sku" value="'+escapeHtml(item.sku||'')+'"></td>'+
      '<td><input data-k="price" type="number" min="0" step="0.01" value="'+(item.price ?? 0)+'"></td>'+
      '<td><input data-k="quantity" type="number" min="0" step="1" value="'+item.quantity+'"></td>'+
      '<td><input data-k="note" value="'+escapeHtml(item.note||'')+'"></td>'+
      '<td>'+
      '<button data-act="save">Save</button> '+
      '<button data-act="del">Delete</button>'+
      '</td>';
    tr.dataset.id = item.id;
    return tr;
  }
  function escapeHtml(s){return String(s).replace(/[&<>"]/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]});}
  function load(){
    fetch(api).then(r=>r.json()).then(function(items){
      tbody.innerHTML='';
      items.forEach(function(it){ tbody.appendChild(row(it)); });
    });
  }
  document.getElementById('createForm').addEventListener('submit', function(e){
    e.preventDefault();
    var fd = new FormData(e.target);
    var data = { name: fd.get('name'), sku: fd.get('sku'), price: parseFloat(fd.get('price')||'0'), quantity: parseInt(fd.get('quantity')||'0',10), note: fd.get('note') };
    fetch(api, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(data)}).then(function(r){
      if(r.status===201){ return r.json(); }
      return r.json().then(function(j){ throw j; });
    }).then(function(){ e.target.reset(); load(); }).catch(function(err){ alert('Error: '+JSON.stringify(err)); });
  });
  tbody.addEventListener('click', function(e){
    var btn = e.target.closest('button'); if(!btn) return;
    var tr = btn.closest('tr'); var id = tr.dataset.id;
    if(btn.dataset.act==='del'){
      fetch(api+'/'+id,{method:'DELETE'}).then(function(r){ if(r.status===204){ load(); } else { return r.json().then(function(j){ throw j; }); } }).catch(function(err){ alert('Error: '+JSON.stringify(err)); });
    }
    if(btn.dataset.act==='save'){
      var data={};
      tr.querySelectorAll('input').forEach(function(inp){ var k=inp.getAttribute('data-k'); if(k==='quantity'){ data[k]= parseInt(inp.value||'0',10); } else if(k==='price'){ data[k]= parseFloat(inp.value||'0'); } else { data[k]= inp.value; } });
      fetch(api+'/'+id,{method:'PUT', headers:{'Content-Type':'application/json'}, body: JSON.stringify(data)}).then(function(r){ if(r.ok){ return r.json(); } return r.json().then(function(j){ throw j; }); }).then(function(){ load(); }).catch(function(err){ alert('Error: '+JSON.stringify(err)); });
    }
  });
  load();
})();
</script>
</body>
</html>
