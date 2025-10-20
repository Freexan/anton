<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Entities</title>
</head>
<body>
<h1>Entities</h1>
<div>
	<form id="createForm">
		<label>Name <input type="text" name="name" required maxlength="100"></label>
		<label>Quantity <input type="number" name="quantity" min="0" value="0"></label>
		<label>Note <input type="text" name="note" maxlength="1000"></label>
		<button type="submit">Add</button>
	</form>
</div>
<hr>
<div>
	<table id="list" border="1" cellpadding="4">
		<thead><tr><th>ID</th><th>Name</th><th>Quantity</th><th>Note</th><th>Actions</th></tr></thead>
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
			'<td><input data-k="quantity" type="number" min="0" value="'+item.quantity+'"></td>'+
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
		var data = { name: fd.get('name'), quantity: parseInt(fd.get('quantity')||'0',10), note: fd.get('note') };
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
			tr.querySelectorAll('input').forEach(function(inp){ var k=inp.getAttribute('data-k'); data[k]= (k==='quantity')? parseInt(inp.value||'0',10) : inp.value; });
			fetch(api+'/'+id,{method:'PUT', headers:{'Content-Type':'application/json'}, body: JSON.stringify(data)}).then(function(r){ if(r.ok){ return r.json(); } return r.json().then(function(j){ throw j; }); }).then(function(){ load(); }).catch(function(err){ alert('Error: '+JSON.stringify(err)); });
		}
	});
	load();
})();
</script>
</body>
</html>
