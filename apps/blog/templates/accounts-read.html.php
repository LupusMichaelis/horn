		  <table>
		    <thead>
			  <tr><th>Title</th><th>Email</th><th>Actions</th></tr>
			</thead>
<?php	foreach($c['accounts'] as $account): ?>
			<tr>
			  <td><?php echo $e->t($account->name) ?></td>
			  <td><?php echo $e->t($account->email) ?></td>
              <td>
			    <ul>
				  <li><a href='/accounts/<?php echo rawurlencode($account->name) ?>?edit'>Edit</a</li>
				  <li><a href='/accounts/<?php echo rawurlencode($account->name) ?>?delete'>Delete</a</li>
				</ul>
			  </td>
			</tr>
<?php	endforeach ?>
          </table>



