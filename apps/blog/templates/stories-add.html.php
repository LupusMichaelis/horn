          <form action='?add' method='POST' accept-charset='utf-8'>
            <p>
              <input type='submit' value='Update'>
            </p>
            <ul>
<?php	foreach($c['stories'] as $story): ?>
              <li>
			    <dl>
                  <dt>Title:</dt>
                  <dd><input name='story.title[]'
							 value='<?php echo $e->a($story->title) ?>'></dd>
				</dl>
				<dl>
                  <dt>Description:</dt>
                  <dd>
				    <textarea name='story.description[]'><?php echo $e->t($story->description) ?></textarea>
				  </dd>
				</dl>
              </li>
<?php	endforeach ?>
            </ul>
          </form>
