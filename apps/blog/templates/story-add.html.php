          <form action='?edit' method='POST' accept-charset='utf-8'>
            <p>
              <input type='submit' value='Update'>
            </p>
            <table>
              <tr>
                <td>Title:</td>
                <td><input name='story.title' value='<?php echo $e->a($c['story']->title) ?>'></td>
              </tr>
              <tr>
                <td>Description:</td>
                <td><input name='story.description' value='<?php echo $e->a($c['story']->description) ?>'></td>
              </tr>
            </table>
          </form>
