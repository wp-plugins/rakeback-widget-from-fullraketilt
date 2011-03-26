<?php
/*
Plugin Name: FullRakeTilt.com Rakeback Widget
Plugin URI: http://www.fullraketilt.com/refer-a-friend/wordpress-rakeback-widget.html
Description: Rakeback Widget to add to your Wordpress Blog. To earn a percentage of rakeback from users that sign up create an account at <a href="http://www.fullraketilt.com" target="_blank">www.FullRakeTilt.com</a> and enter your username in the widget settings. This widget requries the php option 'allow_url_fopen' to be set to <b>true</b> in order to work.
Author: FullRakeTilt
Version: 1.02
Author URI: http://www.FullRakeTilt.com/
*/

function fullraketilt_rakeback_widget() {
  $options = get_option("widget_fullraketilt_rakeback");
  $max_offers = $options['maxOffers'];
  $username = $options['username'];
  $includeImages = $options['includeImages'];
  $showSignupBonus = $options['showSignupBonus'];
  $backgroundColor = $options['backgroundColor'];
  $linkColor = $options['linkColor'];

  // get offers
  $request_url = 'http://www.fullraketilt.com/rakebackoffers.xml';
  $xml = simplexml_load_file($request_url) or die("feed not loading");
  $offers = $xml->xpath('//rakeback/offer');

  $frtURL = "http://www.fullraketilt.com/";
  if ($username != "") {
    $frtURL += "?ref=" . $username;
  }

  if ($options['title'] != "") {
    echo '<h2><a href="'. $frtURL . '" target="_blank" title="Rakeback from FullRakeTilt">' . $options['title'] . "</h2>";
  }
  $background = "";
  if ($backgroundColor != "") {
    $background = 'background-color: ' . $backgroundColor . ';';
  }
  echo "<style>";
  if ($linkColor != "") {
    echo ' #frtrbTable a { color: ' . $linkColor . '; }';
  }
  echo ' #frtrbTable td { vertical-align: middle; padding: 4px;} ';
  echo "</style>";
  echo '<table id="frtrbTable" width="100%" style="text-align: left; ' . $background . '" >';
/*
  echo "<thead><tr>";
  if ($includeImages != "none") {
    echo '<th colspan=2>';
  } else {
    echo '<th>';
  }
  echo "Site</th><th></th>";
  if ($showSignupBonus == "yes_separate_column") {
    echo "<th></th>";
  }
  echo "</tr>";
  echo "</tr></thead>\n";
*/
  foreach ($offers as $offer) {
    $rpurl = $offer->reviewPage;
    $siteName = $offer->sitename;
    if ($username <> "") {
      $rpurl = $rpurl . "?ref=" . $username;
    }
    echo "<tr>";
    $siteAnchor = '<a href="' . $rpurl . '" title="' . $offer->sitename . ' Rakeback" target="_blank">';
    if ($includeImages == "small") {
      echo '<td width="45">' . $siteAnchor . '<img src="' . $offer->tinyImage . '" border=0 width=45 height=15 alt="' . $offer->sitename . ' Rakeback"/>';
      echo '</a></td>';
    } else if ($includeImages == "big") {
      echo '<td width="37">' . $siteAnchor . '<img src="' . $offer->icon . '" border=0 width=37 height=37 alt="' . $offer->sitename . ' Rakeback"/>';
      echo '</a></td>';
    }

    echo '<td>' . $siteAnchor . "<strong>" . $offer->sitename . "</strong>";
    $rbStyle = '';
    if ($showSignupBonus == "yes_under_site_name") {
      echo '<br /><span style="font-size: .75em">' . $offer->bonus . '</span></a></td>';
      $rbStyle = ' style="font-size: 1.3em"';
    } else if ($showSignupBonus == "yes_separate_column") {
       echo '</td><td style="text-align: center;"><span style="font-size: .75em">' . $siteAnchor . $offer->bonus . '</a></span></td>';
    }

    echo '<td ' . $rbStyle . '>' . $siteAnchor . $offer->reward . '</a></td>';

    echo "</tr>";
  }
  echo "</tbody></table>";

}

function init_fullraketilt_rakeback_widget() {
  register_sidebar_widget("FullRakeTilt Rakeback", "fullraketilt_rakeback_widget");
  register_widget_control("FullRakeTilt Rakeback", "fullraketilt_rakeback_control", 500, 600);
}

function fullraketilt_rakeback_control() {
  $options = get_option("widget_fullraketilt_rakeback");
  if (!is_array( $options )) {
    $options = array(
      'title' => 'Rakeback',
      'maxOffers' => '0',
      'includeImages' => 'big',
      'showSignupBonus' => 'yes_under_site_name'
      );
  }

  if ($_POST['frtrb-Submit']) {
    $options['title'] = htmlspecialchars($_POST['frtrb-WidgetTitle']);
    $options['username'] = htmlspecialchars($_POST['frtrb-Username']);
    $options['maxOffers'] = htmlspecialchars($_POST['frtrb-MaxOffers']);
    $options['includeImages'] = htmlspecialchars($_POST['frtrb-IncludeImages']);
    $options['showSignupBonus'] = htmlspecialchars($_POST['frtrb-ShowSignupBonus']);
    $options['backgroundColor'] = htmlspecialchars($_POST['frtrb-BackgroundColor']);
    $options['linkColor'] = htmlspecialchars($_POST['frtrb-LinkColor']);
    update_option("widget_fullraketilt_rakeback", $options);
  }

?>
  <p>
    <label for="frtrb-WidgetTitle">Widget Title: </label>
    <input type="text" id="frtrb-WidgetTitle" name="frtrb-WidgetTitle" value="<?php echo $options['title'];?>" size="50"/>
    <br />
    <label for="frtrb-Username">FullRakeTilt.com Username: </label>
    <input type="text" id="frtrb-Username" name="frtrb-Username" value="<?php echo $options['username'];?>" />
    <br />
    <label for="frtrb-MaxOffers">Max # of Offers to Display: </label>
    <select id="frtrb-MaxOffers" name="frtrb-MaxOffers">
      <option value="0" <?php if ($options['maxOffers'] == 0){ echo "SELECTED"; }?>>All</option>
      <?php
      for ($counter = 1; $counter <=50; $counter += 1) {
      echo "<option value=" . $counter;
      if ($options['maxOffers'] == $counter) { echo " SELECTED"; }
      echo ">" . $counter . "</option>";
      }
      ?>
    </select>
    <br />
    <label for="frtrb-IncludeImages">Include Images: </label>
    <select id="frtrb-IncludeImages" name="frtrb-IncludeImages">
      <option value="small" <?php if ($options['includeImages'] == 'small'){ echo "SELECTED"; }?>>Small 45px x 15px</option>
      <option value="big" <?php if ($options['includeImages'] == 'big'){ echo "SELECTED"; }?>>Big 37px x 37px</option>
      <option value="none" <?php if ($options['includeImages'] == 'none'){ echo "SELECTED"; }?>>No Images</option>
    </select>
    <br />
    <label for="frtrb-ShowSignupBonus">Show Signup Bonus: </label>
    <select id="frtrb-ShowSignupBonus" name="frtrb-ShowSignupBonus">
      <option value="yes_separate_column" <?php if ($options['showSignupBonus'] == 'yes_separate_column'){ echo "SELECTED"; }?>>Yes in Separate Column</option>
      <option value="yes_under_site_name" <?php if ($options['showSignupBonus'] == 'yes_under_site_name'){ echo "SELECTED"; }?>>Yes Under Site Name</option>
      <option value="no" <?php if ($options['showSignupBonus'] == 'no'){ echo "SELECTED"; }?>>No</option>
    </select>
    <br />
    <label for="frtrb-BackgroundColor">Background Color (optional): </label>
    <input type="text" id="frtrb-BackgroundColor" name="frtrb-BackgroundColor" value="<?php echo $options['backgroundColor']; ?>" /> (format: #000000)
    <br />
    <label for="frtrb-LinkColor">Link Color (optional): </label>
    <input type="text" id="frtrb-LinkColor" name="frtrb-LinkColor" value="<?php echo $options['linkColor']; ?>" /> (format: #000000)
    <input type="hidden" id="frtrb-Submit" name="frtrb-Submit" value="1" />
  </p>
<?php
}

add_action("plugins_loaded", "init_fullraketilt_rakeback_widget");

?>