<?php
// Returns a file size limit in bytes based on the PHP upload_max_filesize
// and post_max_size
function file_upload_max_size() {
	static $max_size = -1;
  
	if ($max_size < 0) {
	  // Start with post_max_size.
	  $post_max_size = parse_size(ini_get('post_max_size'));
	  if ($post_max_size > 0) {
		$max_size = $post_max_size;
	  }
  
	  // If upload_max_size is less, then reduce. Except if upload_max_size is
	  // zero, which indicates no limit.
	  $upload_max = parse_size(ini_get('upload_max_filesize'));
	  if ($upload_max > 0 && $upload_max < $max_size) {
		$max_size = $upload_max;
	  }
	}
	return $max_size;
  }
  
  function parse_size($size) {
	$unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
	$size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
	if ($unit) {
	  // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
	  return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
	}
	else {
	  return round($size);
	}
  }

  function formatBytes($size, $precision = 2) {
    $base = log($size, 1024);
    $suffixes = array('', 'K', 'M', 'G', 'T');   

    return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
  }
?>

<h1>Upload medias</h1>
<p>Sur ce serveur, la taille limite d'upload d'un fichier est configuré sur <b><?= formatBytes(file_upload_max_size()) ?></b>.<br/>
Les images de plus de 10M ne sont pas affichées sous forme d'une vignette pour des raisons de performances.</p>
<p>Les fichiers sont stockés dans le dossier <b>collectiveaccess/providence/import/</b></p>
<p><a href="<?= __CA_URL_ROOT__ ?>/index.php/batch/MediaImport/Index"><button>Importer les médias</button></a></p>
<script src="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone-min.js"></script>
<link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet" type="text/css" />

<form action="<?= __CA_URL_ROOT__ ?>/index.php/UploadMedias/Upload/Uploading" class="dropzone">
	<div class="dz-message" data-dz-message><span>Glisser ici les fichiers à uploader</span></div>

</form>

<script>
  // Note that the name "myDropzone" is the camelized
  // id of the form.
  Dropzone.options.myDropzone = {
    // Configuration options go here
	maxFilesize: <?= floor(file_upload_max_size()/(1024*1024)) ?>,
	maxThumbnailFilesize: 10,
	addRemoveLinks: true,
	init: function () {
        let myDropzone = this;
		this.on("removedfile", function(file) {
			$(".dz-message").hide();
			$.ajax({
				type: 'get',
				url: '<?= __CA_URL_ROOT__ ?>/index.php/UploadMedias/Upload/DeleteMedia/name/'+file.name,
				success: function(data, textStatus, jqXHR){
					console.log("textStatus", textStatus);
					$(".dz-message").hide();
				},
				error: function(xhr, durum, hata) {
					alert("Hata: " + hata);
				}
        	});
		});
        $.ajax({
            type: 'get',
            url: '<?= __CA_URL_ROOT__ ?>/index.php/UploadMedias/Upload/AjaxGetMedias',
            success: function(mocks){
				console.log("mockFile", mocks);
                $.each(mocks, function(key,value) {
                    let mockFile = { name: value.name, size: value.size };
					console.log("mockFile", mockFile);
                    myDropzone.displayExistingFile(mockFile, value.url);
                });
            },
            error: function(xhr, durum, hata) {
                alert("Hata: " + hata);
            }
        });
    }
  };
  let myDropzone = new Dropzone("form.dropzone", Dropzone.options.myDropzone);
</script>
<style>
	.dropzone {
		display:block;
		height:calc(100vh - 150px);
	}
</style>