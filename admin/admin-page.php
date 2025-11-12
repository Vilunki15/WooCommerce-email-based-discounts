<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function admin_page_render() {
    if ( isset( $_POST['save'] ) ) {
        $data = [];
        if ( isset( $_POST['type'] ) ) {
            foreach ( $_POST['type'] as $i => $type ) {
                $data[] = [
                    'type' => sanitize_text_field( $type ),
                    'name' => sanitize_text_field( $_POST['name'][$i] ?? '' ),
                    'email' => sanitize_text_field( $_POST['email'][$i] ?? '' ),
                    'terms' => sanitize_text_field( $_POST['terms'][$i] ?? '' ),
                    'discount' => sanitize_text_field( $_POST['discount'][$i] ?? '' )
                ];
            }
        }
        update_option( 'discount_data', $data );
        echo '<div class="updated"><p>Tiedot tallennettu!</p></div>';
    }

    $rows = get_option( 'discount_data', [] );
    ?>
    <div class="wrap">
        <h1>Alennukset</h1>
        <form method="post">
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>Asiakastyyppi</th>
                        <th>Nimi</th>
                        <th>S-posti / domain</th>
                        <th>Maksuehto</th>
                        <th>Alennus-%</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <?php foreach ( $rows as $i => $row ): ?>
                    <tr>
                        <td><input name="type[]" value="<?php echo esc_attr($row['type']); ?>" /></td>
                        <td><input name="name[]" value="<?php echo esc_attr($row['name']); ?>" /></td>
                        <td><input name="email[]" value="<?php echo esc_attr($row['email']); ?>" /></td>
                        <td><input name="terms[]" value="<?php echo esc_attr($row['terms']); ?>" /></td>
                        <td><input name="discount[]" value="<?php echo esc_attr($row['discount']); ?>" /></td>
                        <td><button type="button" class="button remove">Poista</button></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><button type="button" class="button" id="add-row">+ Lisää rivi</button></p>
            <p><input type="submit" name="save" class="button-primary" value="Tallenna muutokset"></p>
        </form>
    </div>

    <script>
    document.getElementById('add-row').addEventListener('click', function() {
        const body = document.getElementById('table-body');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input name="type[]" /></td>
            <td><input name="name[]" /></td>
            <td><input name="email[]" /></td>
            <td><input name="terms[]" /></td>
            <td><input name="discount[]" /></td>
            <td><button type="button" class="button remove">Poista</button></td>
        `;
        body.appendChild(tr);
    });
    document.addEventListener('click', e => {
        if (e.target.classList.contains('remove')) e.target.closest('tr').remove();
    });
    </script>
    <?php
}
