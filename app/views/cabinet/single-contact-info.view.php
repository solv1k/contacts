<?php 
  $contact = $_view->data('contact'); 
?>

<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="universalModalLabel">Данные контакта</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="modal-body">
      <div class="single-contact-info">
        <p>Имя: <b><?php echo $contact->name; ?></b></p>
        <p>Телефон: <b><?php echo $contact->phone; ?></b></p>
        <p>Телефон (прописью): <b><?php echo (new \MessageFormatter('ru-RU', '{n, spellout}'))->format(['n' => $contact->phone]); ?></b></p>
        <p>Добавлен: <b><?php echo $contact->created_at; ?></b></p>

        <?php if ($contact->photoUrl()) { ?>
          <div class="photo"><img class="img-fluid" src="<?php echo $contact->photoUrl(); ?>" alt="Фото <?php echo $contact->name; ?>"></div>
        <?php } ?>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
    </div>
  </div>
</div>