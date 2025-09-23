jQuery(document).ready(function($){
function showMessage(msg, type){
$('#filepress-messages').text(msg).attr('class', type);
setTimeout(function(){ $('#filepress-messages').text('').attr('class', ''); }, 4000);
}


function fetchList(){
$.post(filepress_vars.ajax_url, { action: 'filepress_list', nonce: filepress_vars.nonce }, function(res){
if(res.success){
let html = '<table class="filepress-table"><thead><tr><th>Name</th><th>Size</th><th>Actions</th></tr></thead><tbody>';
res.data.forEach(function(f){
html += '<tr>'+
'<td>'+ f.name +'</td>'+
'<td>'+ Math.round(f.size/1024) +' KB</td>'+
'<td>'+
'<a href="'+ f.url +'" target="_blank">Download</a> | '+
'<a href="#" class="filepress-rename" data-name="'+ f.name +'">Rename</a> | '+
'<a href="#" class="filepress-delete" data-name="'+ f.name +'">Delete</a>'+
'</td>'+
'</tr>';
});
html += '</tbody></table>';
$('#filepress-list').html(html);
} else {
$('#filepress-list').html('<p>No files found.</p>');
}
});
}


// Initial load
fetchList();


// Upload
$('#filepress-upload').on('click', function(e){
e.preventDefault();
let file = $('#filepress-file')[0].files[0];
if(!file){ showMessage('Please choose a file', 'error'); return; }


let fd = new FormData();
fd.append('action', 'filepress_upload');
fd.append('nonce', filepress_vars.nonce);
fd.append('file', file);


$.ajax({
url: filepress_vars.ajax_url,
type: 'POST',
contentType: false,
processData: false,
data: fd,
success: function(res){
if(res.success){
showMessage('Uploaded: ' + res.data.url, 'success');
fetchList();
} else {
showMessage('Error: ' + res.data, 'error');
}
}
});
});


// Delete
$(document).on('click', '.filepress-delete', function(e){
e.preventDefault();
if(!confirm('Delete this file?')) return;
let name = $(this).data('name');
$.post(filepress_vars.ajax_url, { action: 'filepress_delete', nonce: filepress_vars.nonce, name: name }, function(res){
if(res.success){ showMessage('Deleted', 'success'); fetchList(); } else { showMessage('Could not delete', 'error'); }
});
});


// Rename (simple prompt)
$(documen