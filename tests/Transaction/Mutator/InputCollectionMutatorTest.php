<?php


namespace BitWasp\Bitcoin\Tests\Transaction\Mutator;

use BitWasp\Bitcoin\Collection\Transaction\TransactionInputCollection;
use BitWasp\Bitcoin\Script\Script;
use BitWasp\Bitcoin\Tests\AbstractTestCase;
use BitWasp\Bitcoin\Transaction\Mutator\InputCollectionMutator;
use BitWasp\Bitcoin\Transaction\Mutator\InputMutator;
use BitWasp\Bitcoin\Transaction\TransactionInput;
use BitWasp\Buffertools\Buffer;

class InputCollectionMutatorTest extends AbstractTestCase
{

    public function testMutatesOutputCollection()
    {
        $vout1 = -1;
        $script1 = new Script(new Buffer('0'));
        $vout2 = -2;
        $script2 = new Script(new Buffer('1'));
        $collection = new TransactionInputCollection([
            new TransactionInput('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 0, new Script()),
            new TransactionInput('baaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 0, new Script()),
        ]);

        $mutator = new InputCollectionMutator($collection->all());
        $mutator[0]->script($script1)->vout($vout1);

        $mutator[1]->script($script2)->vout($vout2);

        $new = $mutator->done();
        $this->assertEquals('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', $new[0]->getTransactionId());
        $this->assertEquals($vout1, $new[0]->getVout());
        $this->assertEquals($script1, $new[0]->getScript());
        $this->assertEquals('baaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', $new[1]->getTransactionId());
        $this->assertEquals($vout2, $new[1]->getVout());
        $this->assertEquals($script2, $new[1]->getScript());
    }


    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidSlice()
    {
        $collection = new TransactionInputCollection([
        ]);

        $mutator = new InputCollectionMutator($collection->all());
        $mutator->slice(0, 1);
    }

    public function testNull()
    {
        $collection = new TransactionInputCollection([
            new TransactionInput('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 5, new Script()),
            new TransactionInput('baaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 10, new Script()),
        ]);

        $mutator = new InputCollectionMutator($collection->all());
        $mutator->null();
        $outputs = $mutator->done();

        $this->assertEquals(0, count($outputs));
    }
}
