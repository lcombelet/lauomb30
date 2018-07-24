<!DOCTYPE html>
<html>
<head>
<?php include 'head.php';?>
</head>
<body>

<?php include 'header.php';?>

<div class="row">
  <div class="leftcolumn">
    <div class="card">
      <h2>WELCOME TO MY WEBSITE</h2>
      <h5>A note from the nerd</h5>
      <p>You are looking at the landing page of a project that started way back in 2001 or so, when there was an urge to make an database accessible over the web. Now, <?php $timepassed=date('Y')-2001; echo $timepassed; ?> years later, we are here. As the subtitle so subtly suggests, this website is a hobby gone full-nerd!</p>
      <p>Whilst there are a many nice things that I would love to show you, please go ahead and create an account first. I'd like to know who is looking at my stuff <i class="far fa-smile-wink"></i>.</p>
      <p>After your account has been activated, you can also contact me with feedback or cool stuff that I should also include.</p>
    </div>
    <div class="card">
      <h2>IN ORDER TO AVOID ISSUES..</h2>
      <h5>A list of things that make up this website</h5>
      <p>This never-ending project started officially in <a href="https://products.office.com/en/access" target="_blank">MS Access</a>. Teaching myself pretty much everything byt the use of online tutorials, below you find a list of functionalities, languages, programmes and features all combined for my, euhm your, entertainment.</p>
      <p>One word beforehand, I am definitely not the most technical skilled person! I just like to learn new things and mess around with code programming until I get frustrated and throw everything away. There are a billion people on this planet that can do things faster and better. I am in it for the fun.</p>
      <p>So here we go..</p>
      <p>Basic stuff first: this website uses a combination of HTML, PHP, MySQL and CSS. For me this is complex enough, and provides me with all that I currently need as far as functionality goes. There are some javascript functions included, but they are mainly taken from templates and I am still learning how to write that code. All the other languages I learnt from <a href="https://www.w3schools.com/" target="_blank">W3 Schools</a> and <a href="https://stackoverflow.com/" target="_blank">Stack Overflow</a>. </p>
      <p>With regards to external sources that I use. They are:
        <ul>
          <li>The font (Karla) is taken from <a href="https://fonts.google.com/specimen/Karla" target="_blank">Google Fonts</a>, integrated through CSS. It's simple, clearly readable and looks nice.</li>
          <li>The icons I use everywhere are taken from <a href="https://fontawesome.com" target="_blank">Font Awesome</a>, also through CSS. The interface is very easy to use, and since all icons are font-based they remain sharp even when scaled.</li>
          <li>The entire two column layout is actually one of the basic templates from <a href="https://www.w3schools.com/css/css_website_layout.asp" target="_blank">W3 School</a>. I chose this layout as it is pretty straigtforward and minimalistic.</li>
          <li>Colorscheme is made up by myself. As I am colorblind the palette might not be the best ever. At least I am able to see the different colors so I don't care, hehe.</li>
          <li><a href="http://jqueryui.com/" target="_blank">JQuery</a> is used for form manipulations, easy to integrate and a sh*tload of functions that I can use.</li>
          <li>I used <a href="https://www.highcharts.com/" target="_blank">Highcharts</a> in the past for table formatting. As I started all over with the current layout I am considering customizing my own table layout. It worked for me in the past but at the moment not a priority.</li>
          <li><a href="https://developers.google.com/chart/" target="_blank">Google Charts</a> everywhere! Pretty much all database integrations are focused on data visualizations. Still learning to work with the required format but over time only this channel will be used.</li>
          <li>Everything is built in <a href="https://atom.io/" target="_blank">Atom</a> in a DEV environment. This is a Windows based machine with locally installed servers for testing.</li>
          <li>When a functionality is finished, I use <a href="https://github.com/" target="_blank">GitHUB</a> integration for versioning and push the functionality to the PROD environment.</li>
          <li>The PROD environment is a Linux based (<a href="https://www.ubuntu.com/" target="_blank">Ubuntu</a>) server running a <a href="https://www.nginx.com/" target="_blank">Nginx</a> webserver.</li>
        </ul>
      </p>
    </div>
  </div>
  <div class="rightcolumn">
    <?php include 'aboutme.php';?>
    <?php include 'social.php';?>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
