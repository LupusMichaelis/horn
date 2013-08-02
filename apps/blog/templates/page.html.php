<?php
/** HTML Page template
 *
 *  \project	Horn Framework <http://horn.lupusmic.org>
 *  \author		Lupus Michaelis <mickael@lupusmic.org>
 *  \copyright	2013, Lupus Michaelis
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

  <div>
    <ul>
      <li><a href='/stories'>Stories</a></li>
      <li><a href='/users'>Users</a></li>
    </ul>
  </div>

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
      <ul>
        <li><a href='?add'>Add</a></li>
      </ul>
<?php if(0 < count($c->errors)): ?>
        <div class='error'>
          <h2><?php echo $e->t($c->errors[0]);?></h2>
        </div>
<?php else: ?>
        <div>
<?php if($c->results->has_key($c->params['resource'])): ?>
<?php    echo $this->r($c->params['resource']
            , $c->params['action']
            , $c->params['type']
            , array($c->params['resource'] => $c->results[$c->params['resource']])) ?>
<?php else: ?>
        <p>No story.</p>
<?php endif ?>
        </div>
    </div>
  </body>
</html>
