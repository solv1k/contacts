<?php 
  $user = $_view->data('user'); 
  $sort = $_view->data('sort', 'date_desc');
  $contacts = $_view->data('contacts', $user->contacts()->customSort($sort)->get());
?>

<div class="container mb-3">
  <div class="row">
    <div class="col"><h4>Список контактов</h4></div>
    <div class="col">
      <?php if ($contacts) { ?>
      <select id="contactsListSort" class="form-control form-control-sm">
        <option value="date_desc" <?php echo $sort == 'date_desc' ? 'selected="selected"' : ''; ?>>Сначала новые</option>
        <option value="date_asc" <?php echo $sort == 'date_asc' ? 'selected="selected"' : ''; ?>>Сначала старые</option>
      </select>
      <?php } ?>  
    </div>
  </div>
</div>

<div class="contacts-list">
<?php if ($contacts) { ?>
  <?php foreach ($contacts as $contact) { ?>
    
    <div class="contact" data-id="<?php echo $contact->id; ?>">
      <div class="buttons">
        <button class="btn btn-primary btn-modal-contact" data-action="view"><i class="fa fa-eye"></i> Подробнее</button>
        <button class="btn btn-success btn-modal-contact" data-action="edit"><i class="fa fa-edit"></i> Править</button>
        <button class="btn btn-danger btn-modal-contact" data-action="remove"><i class="fa fa-trash"></i> Удалить</button>
        <button class="btn btn-secondary btn-hider"><i class="fa fa-cancel"></i> Отмена</button>
      </div>
      <div class="row">
        <div class="col-sm-2">
          <?php if ($contact->photoUrl()) { ?>
            <div class="photo"><img src="<?php echo $contact->photoUrl(); ?>" alt="Фото <?php echo $contact->name; ?>"></div>
          <?php } else { ?>
            <div class="photo"><i class="fa fa-user"></i></div>
          <?php } ?>
        </div>
        <div class="col-sm-6">
          <div>Имя: <strong><?php echo $contact->name; ?></strong></div>
          <div>Телефон: <strong><?php echo $contact->phone; ?></strong></div>
        </div>
        <div class="col-sm-4 text-right">
          <button class="btn btn-link"><i class="fa fa-edit"></i></button>
        </div>
      </div>
    </div>

  <?php } ?>
<?php } else { ?>
  <div class="alert alert-info"><i class="fa fa-info-circle"></i> <b>В вашем списке нет контактов.</b><br><br>Воспользуйтесь формой для создания нового контакта.</div>
<?php } ?>  
</div>