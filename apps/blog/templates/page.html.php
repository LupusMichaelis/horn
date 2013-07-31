<?php
/** HTML Page template
 *
 *  Project	Horn Framework <http://horn.lupusmic.org>
 *  \author		Lupus Michaelis <mickael@lupusmic.org>
 *  Copyright	2011, Lupus Michaelis
 *  License	AGPL <http://www.fsf.org/licensing/licenses/agpl-3.0.html>
 */

/*
 *  This file is part of Horn Framework.
 *
 *  Horn Framework is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Horn Framework is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero Public License for more details.
 *
 *  You should have received a copy of the GNU Affero Public License
 *  along with Horn Framework.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
?><!DOCTYPE html>
<html>
  <head>
    <title><?php echo $e->t($c->doc->title) ?></title>
  </head>
  <body>

  <div></div>

<?php foreach($c->doc->scripts as $resource): ?>
    <script type='<?php echo $e->a($resource['type']) ?>'
            src='<?php echo $e->a($resource['src']) ?>'
            ></script>
<?php endforeach ?>

<?php foreach($c->doc->styles as $resource): ?>
    <link type='<?php echo $e->a($resource['type']) ?>'
          href='<?php echo $e->a($resource['src']) ?>'
          rel='stylesheet'
          />
<?php endforeach ?>
    <div>
<?php if(0 < count($c->errors)): ?>
        <div class='error'>
          <h2><?php echo $e->t($c->errors[0]);?></h2>
        </div>
<?php else: ?>
        <div>
<?php echo $this->r($c->params['resource']
        , $c->params['action']
        , $c->params['type']
        , $c->results['story']); ?>
        </div>
<?php endif ?>
    </div>
  </body>
</html>
