<?php

namespace Rector\EarlyReturn\Tests\Rector\If_\RemoveAlwaysElse\ElseIf_;

class SomeClass
{
    public function run()
    {
        if ($cond1) {
            return 'foo';
        } elseif ($cond2) {
            bar();
        } elseif ($cond3) {
            baz();
        } else {
            foo();
        }
    }
}

?>
-----
<?php

namespace Rector\EarlyReturn\Tests\Rector\If_\RemoveAlwaysElse\ElseIf_;

class SomeClass
{
    public function run()
    {
        if ($cond1) {
            return 'foo';
        }
        if ($cond2) {
            bar();
        } elseif ($cond3) {
            baz();
        } else {
            foo();
        }
    }
}

?>
