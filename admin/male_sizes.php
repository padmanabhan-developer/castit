<div class="col3">
  <label class="ansog_select_label">SKJORTE STR.</label>
  <div class="custom-select">
  <!-- shirt size / skjorte fra-->
    <select class="shirt_size_from">
      <?php
      echo "<option value=''> - </option>";
      for ($i=35; $i < 47; $i++) {
        $selected = '';
        if($i == $value['shirt_size_from']){
          $selected = 'selected=selected';
        }
        echo "<option value='".$i."' ".$selected.">".$i."''</option>";
      }
      ?>
    </select>
  </div>
</div>

<div class="col3">
  <label class="ansog_select_label">__</label>
  <div class="custom-select">
  <!-- shirt size / skskjorteorte til-->
    <select class="shirt_size_to">
    <?php
    echo "<option value=''> - </option>";
    for ($i=35; $i < 47; $i++) { 
      $selected = '';
      if($i == $value['shirt_size_to']){
        $selected = 'selected=selected';
      }
    echo "<option value='".$i."' ".$selected.">".$i."''</option>";
    }
    ?>
    </select>
  </div>
</div>

<div class="col3">
  <label class="ansog_select_label">bukser str.</label>
  <div class="custom-select">
  <!-- pant size / bukser fra -->
    <select class="pants_size_from">
    <?php
    echo "<option value=''> - </option>";
    for ($i=26; $i < 43; $i++) { 
      $selected = '';
      if($i == $value['pants_size_from']){
        $selected = 'selected=selected';
      }
      echo "<option value='".$i."' ".$selected.">".$i."''</option>";
    }
    ?>
    </select>
  </div>
</div>

<div class="col3">
  <label class="ansog_select_label">__</label>
  <div class="custom-select">
    <!-- pant size / bukser til-->
    <select class="pants_size_to">
    <?php
      echo "<option value=''> - </option>";
      for ($i=26; $i < 43; $i++) { 
        $selected = '';
        if($i == $value['pants_size_to']){
          $selected = 'selected=selected';
        }
        echo "<option value='".$i."' ".$selected.">".$i."''</option>";
      }
    ?>
    </select>
  </div>
</div>

<div class="col3">
  <label class="ansog_select_label">sko str.</label>
  <div class="custom-select">
    <!-- shoe size / sko fra-->
    <select class="shoe_size_from">
    <?php
      echo "<option value=''> - </option>";
      for ($i=34; $i < 49; $i++) { 
        $selected = '';
        if($i == $value['shoe_size_from']){
          $selected = 'selected=selected';
        }
        echo "<option value='".$i."' ".$selected.">".$i."''</option>";
      }
    ?>
    </select>
  </div>
</div>

<div class="col3">
  <label class="ansog_select_label">__</label>
  <div class="custom-select">
    <!-- shoe size / sko til-->
    <select class="shoe_size_to">
    <?php
      echo "<option value=''> - </option>";
      for ($i=34; $i < 49; $i++) { 
        $selected = '';
        if($i == $value['shoe_size_to']){
          $selected = 'selected=selected';
        }
        echo "<option value='".$i."' ".$selected.">".$i."''</option>";
      }
    ?>
    </select>
  </div>
</div>


<div class="col3">
<label class="ansog_select_label">jakkesæt str.</label>
<div class="custom-select">
<!-- suit size / jakkesæt fra -->
<select class="suite_size_from">
<?php
echo "<option value=''> - </option>";
for ($i=44; $i < 63; $i=$i+2) { 
$selected = '';
if($i == $value['suite_size_from']){
$selected = 'selected=selected';
}
echo "<option value='".$i."' ".$selected.">".$i."''</option>";
}
?>
</select>
</div>
</div>

<div class="col3">
<label class="ansog_select_label">__</label>
<div class="custom-select">
<!-- suit size / jakkesæt til-->
<select class="suite_size_to">
<?php
echo "<option value=''> - </option>";
for ($i=44; $i < 63; $i=$i+2) { 
$selected = '';
if($i == $value['suite_size_to']){
$selected = 'selected=selected';
}
echo "<option value='".$i."' ".$selected.">".$i."''</option>";
}
?>
</select>
</div>
</div>
