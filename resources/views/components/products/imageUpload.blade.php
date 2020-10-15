<style>
  .iu-header {
    margin-bottom: 8px;
  }

  .iu-body {
    height: 150px;
    width: 150px;

    position: relative;
  }

  .iu-body:hover .{{ isset($name) ? "iu-overlay-{$name}" : 'iu-overlay' }} {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
  }

  .{{ isset($name) ? "iu-image-{$name}" : 'iu-image' }} {
    border: 1px solid #000000;

    height: 100%;
    width: 100%;
  }

  .{{ isset($name) ? "iu-overlay-{$name}" : 'iu-overlay' }}  {
    background: rgba(0, 0, 0, 0.5);
    color: rgba(255, 255, 255, 1);
    cursor: pointer;

    display: none;

    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
  }

  .iu-label-icon {
    font-size: 3em;
    margin-bottom: 5px;
  }
</style>


<div class="iu-section">
  <div class="iu-header">
    <strong>{{ $slot }}</strong>
  </div><!--/.iu-header-->

  <div class="iu-body">
    <img class='{{ isset($name) ? "iu-image-{$name}" : "iu-image" }}' src="{{ url($imageSource ?? "storage/products/display_pictures/default.jpg") }}"/>
    <div class='{{ isset($name) ? "iu-overlay-{$name}" : "iu-overlay" }}'>
      <i class="fa fa-camera iu-label-icon"></i>
      <span class="iu-label">Change Picture</span>
    </div><!--/.iu-overlay-->
  </div><!--/.iu-body-->

  <input name='{{ isset($name) ? "image-{$name}" : "image" }}' type="file" style="display:none;" />
  <br />
</div><!--/.iu-section-->


<script>
  let inputFileEl{{ $name }} = document.getElementsByName('{{ isset($name) ? "image-{$name}" : "image" }}')[0]
  let uploadButtonEl{{ $name }} = document.getElementsByClassName('{{ isset($name) ? "iu-overlay-{$name}" : "iu-overlay" }}')[0]
  let imageEl{{ $name }} = document.getElementsByClassName('{{ isset($name) ? "iu-image-{$name}" : "iu-image" }}')[0]

  inputFileEl{{ $name }}.addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        imageEl{{ $name }}.src = e.target.result
      }

      reader.readAsDataURL(this.files[0]);
    }
  })

  uploadButtonEl{{ $name }}.addEventListener('click', function(e) {
    inputFileEl{{ $name }}.click()
  })
</script>