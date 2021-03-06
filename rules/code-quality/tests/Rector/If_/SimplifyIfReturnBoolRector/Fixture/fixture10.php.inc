<?php

namespace Rector\CodeQuality\Tests\Rector\If_\SimplifyIfReturnBoolRector\Fixture;

class AnyClass
{
    public function reset()
    {
        if ($this->getValue()) {
            return true;
        }
        return false;
    }

    public function getValue(): ?int
    {
        return 5;
    }
}

?>
-----
<?php

namespace Rector\CodeQuality\Tests\Rector\If_\SimplifyIfReturnBoolRector\Fixture;

class AnyClass
{
    public function reset()
    {
        return (bool) $this->getValue();
    }

    public function getValue(): ?int
    {
        return 5;
    }
}

?>
