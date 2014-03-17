          <form action='<?php echo $e->a($c['account']->name) ?>?edit' method='POST' accept-charset='utf-8'>
            <p>
              <input type='submit' value='Update'>
            </p>
            <table>
              <tr>
                <td>Name:</td>
                <td><input name='account.name' value='<?php echo $e->a($c['account']->name) ?>'></td>
              </tr>
              <tr>
                <td>Email:</td>
                <td><input name='account.email' value='<?php echo $e->a($c['account']->email) ?>'></td>
              </tr>
            </table>
          </form>
