<footer
  class="wrapper"
  data-name="cta"
>
  <?php if (isset($p) && $p->field('show_cta')) { ?>
    <?php
    $title = $p->field('cta_title') ?: get_field('cta_title', 'option');
    $text = $p->field('cta_text') ?: get_field('cta_text', 'option');
    $buttons = $p->field('cta_buttons') ?: get_field('cta_buttons', 'option');
    ?>

    <div class="bg-primary-50 md:py-15 rounded-lg px-5 py-10">
      <div class="mx-auto flex w-full max-w-[720px] flex-col items-center text-center">
        <h2><?= $title; ?></h2>

        <?= $text; ?>

        <?= component('buttons', [
          'buttons' => $buttons,
          'class' => 'justify-center',
        ]); ?>
      </div>
    </div>
  <?php } else { ?>
    <hr />
  <?php } ?>
</footer>
