<?php
/**
 * Created by PhpStorm.
 * User: shontauro
 * Date: 2018-12-05
 * Time: 18:57
 */

if (!empty($_POST["state"])) {

    $jsonStates = json_decode(file_get_contents(dirname(__FILE__) . '/assets/states.json'), true);
    foreach ($jsonStates as $key => $value) {
        if ($value["departamento"] === $_POST["state"]) {
            foreach ($value["ciudades"] as $city) {
                ?>
                <option value="<?php echo $city; ?>"><?php echo $city; ?></option>
                <?php
            }
        }
    }
}
?>