<style>
  .preview-frame {
    display: none;
    position: absolute;
    top: 50px;
    left: 50%;
    transform: translateX(-50%);
    width: 60%;
    height: 80%;
    border: 2px solid #007bff;
    background: white;
    z-index: 1000;
    box-shadow: 0 0 15px rgba(0,0,0,0.3);
  }
</style>

<div id="contractPreview" class="preview-frame">
    <iframe id="contractIframe" width="100%" height="100%"></iframe>
</div>
