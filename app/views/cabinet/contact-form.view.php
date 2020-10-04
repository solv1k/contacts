<h4 class="mb-4">Создание нового контакта</h4>

<form data-action="contact.add" data-on-success="reloadContactsList" class="cabinet-form contact-add-form mb-4">
  <input id="contactName" name="contactName" type="text" class="form-control" placeholder="Имя контакта" data-label="Имя контакта" data-validate="required|alphaNumSpaces|minLength:3|maxLength:100">
  <input id="contactPhone" name="contactPhone" type="text" class="form-control" placeholder="Номер телефона" data-label="Номер телефона" data-validate="required|phone">
  
  <div class="file-uploader">
    <label for="contactPhoto"><i class="fa fa-image"></i> <span class="empty">Добавить фото (png или jpg)</span></label>
    <input class="d-none" type="file" id="contactPhoto" name="contactPhoto" data-label="Фото контакта" data-image-container="#contactPhotoPreview" data-validate="image" accept="image/jpg,image/png">
  </div>

  <div id="contactPhotoPreview" class="photo-preview mb-4">
    <h4>Превью</h4>
    <img src="" alt="Превью контакта" class="img-fluid">
  </div>

  <div class="btn btn-primary btn-send-ajax-form mt-3"><i class="fa fa-save"></i> Сохранить в мои контакты</div>
</form>

<div id="success" class="alert alert-sys alert-success"><i class="fa fa-check"></i> <div class="html"></div></div>
<div id="error" class="alert alert-sys alert-danger"><i class="fa fa-exclamation-triangle"></i> <div class="html"></div></div>
<div id="info" class="alert alert-sys alert-info"><i class="fa fa-info-circle"></i> <div class="html"></div></div>