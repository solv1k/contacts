<?php 
  $contact = $_view->data('contact'); 
?>

<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="universalModalLabel">Редактирование контакта</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="modal-body">
      <div class="single-contact-info">
        <form data-action="contact.edit.accept" data-on-success="reloadContactsListAfterEdit" class="cabinet-form contact-add-form mb-4">
          <input type="hidden" name="contactEditId" value="<?php echo $contact->id; ?>">

          <input id="contactEditName" name="contactEditName" type="text" class="form-control" placeholder="Имя контакта" data-label="Имя контакта" data-validate="required|alphaNumSpaces|minLength:3|maxLength:100" value="<?php echo $contact->name; ?>">
          <input id="contactEditPhone" name="contactEditPhone" type="text" class="form-control" placeholder="Номер телефона" data-label="Номер телефона" data-validate="required|phone" value="<?php echo $contact->phone; ?>">
          
          <div class="file-uploader">
            <label for="contactEditPhoto"><i class="fa fa-image"></i> <span class="empty"><?php echo $contact->photo ? 'Сменить' : 'Добавить' ; ?> фото (png или jpg)</span></label>
            <input class="d-none" type="file" id="contactEditPhoto" name="contactEditPhoto" data-label="Фото контакта" data-image-container="#contactEditPhotoPreview" data-validate="image" accept="image/jpg,image/png">
          </div>

          <div id="contactEditPhotoPreview" class="photo-preview mb-4" style="<?php echo $contact->photo ? 'display: block;' : ''; ?>">
            <h4>Превью</h4>
            <img src="<?php echo $contact->photo ? $contact->photoUrl() : ''; ?>" alt="Превью контакта" class="img-fluid">
          </div>

          <div class="btn btn-primary btn-send-ajax-form mt-3"><i class="fa fa-save"></i> Сохранить изменения</div>
        </form>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
    </div>
  </div>
</div>