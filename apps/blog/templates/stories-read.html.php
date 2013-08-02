		  <table>
		    <thead>
			  <tr><th>Title</th><th>Actions</th></tr>
			</thead>
<?php	foreach($c['stories'] as $story): ?>
			<tr>
			  <td><?php echo $e->t($story->title) ?></td>
              <td>
			    <ul>
				  <li><a href='/stories/<?php echo rawurlencode($e->t($story->title)) ?>?edit'>Edit</a</li>
				</ul>
			  </td>
			</tr>
<?php	endforeach ?>
          </table>


