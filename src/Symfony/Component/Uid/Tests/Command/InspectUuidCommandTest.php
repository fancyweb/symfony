<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Uid\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Uid\Command\InspectUuidCommand;

final class InspectUuidCommandTest extends TestCase
{
    public function testInvalid()
    {
        $commandTester = new CommandTester(new InspectUuidCommand());

        $this->assertSame(1, $commandTester->execute(['uuid' => 'foobar']));
        $this->assertStringContainsString('Invalid UUID "foobar".', str_replace("\n", ' ', $commandTester->getDisplay(true)));
    }

    public function testNil()
    {
        $commandTester = new CommandTester(new InspectUuidCommand());

        $this->assertSame(0, $commandTester->execute(['uuid' => '00000000-0000-0000-0000-000000000000']));
        $this->assertSame(<<<EOF
 ---------------------- -------------------------------------- 
  Label                  Value                                 
 ---------------------- -------------------------------------- 
  Version                nil                                   
  Canonical (RFC 4122)   00000000-0000-0000-0000-000000000000  
  Base 58                1111111111111111111111                
  Base 32                00000000000000000000000000            
 ---------------------- -------------------------------------- 


EOF
                , $commandTester->getDisplay(true));
    }

    public function testUnknown()
    {
        $commandTester = new CommandTester(new InspectUuidCommand());

        $this->assertSame(0, $commandTester->execute(['uuid' => '461cc9b9-2397-0dba-91e9-33af4c63f7ec']));
        $this->assertSame(<<<EOF
 ---------------------- -------------------------------------- 
  Label                  Value                                 
 ---------------------- -------------------------------------- 
  Version                unknown                               
  Canonical (RFC 4122)   461cc9b9-2397-0dba-91e9-33af4c63f7ec  
  Base 58                9f9nftX6dw4oVPm5uT17um                
  Base 32                263K4VJ8WQ1PX93T9KNX667XZC            
 ---------------------- -------------------------------------- 


EOF
            , $commandTester->getDisplay(true));

        $this->assertSame(0, $commandTester->execute(['uuid' => '461cc9b9-2397-2dba-91e9-33af4c63f7ec']));
        $this->assertSame(<<<EOF
 ---------------------- -------------------------------------- 
  Label                  Value                                 
 ---------------------- -------------------------------------- 
  Version                unknown                               
  Canonical (RFC 4122)   461cc9b9-2397-2dba-91e9-33af4c63f7ec  
  Base 58                9f9nftX6fjLfNnvSAHMV7Z                
  Base 32                263K4VJ8WQ5PX93T9KNX667XZC            
 ---------------------- -------------------------------------- 


EOF
            , $commandTester->getDisplay(true));

        $this->assertSame(0, $commandTester->execute(['uuid' => '461cc9b9-2397-7dba-91e9-33af4c63f7ec']));
        $this->assertSame(<<<EOF
 ---------------------- -------------------------------------- 
  Label                  Value                                 
 ---------------------- -------------------------------------- 
  Version                unknown                               
  Canonical (RFC 4122)   461cc9b9-2397-7dba-91e9-33af4c63f7ec  
  Base 58                9f9nftX6kE2K6HpooNEQ83                
  Base 32                263K4VJ8WQFPX93T9KNX667XZC            
 ---------------------- -------------------------------------- 


EOF
            , $commandTester->getDisplay(true));

        $this->assertSame(0, $commandTester->execute(['uuid' => '461cc9b9-2397-cdba-91e9-33af4c63f7ec']));
        $this->assertSame(<<<EOF
 ---------------------- -------------------------------------- 
  Label                  Value                                 
 ---------------------- -------------------------------------- 
  Version                unknown                               
  Canonical (RFC 4122)   461cc9b9-2397-cdba-91e9-33af4c63f7ec  
  Base 58                9f9nftX6pihxonjBST7K8X                
  Base 32                263K4VJ8WQSPX93T9KNX667XZC            
 ---------------------- -------------------------------------- 


EOF
            , $commandTester->getDisplay(true));
    }

    public function testV1()
    {
        $commandTester = new CommandTester(new InspectUuidCommand());

        $this->assertSame(0, $commandTester->execute(['uuid' => '4c8e3a2a-5993-11eb-a861-2bf05af69e52']));
        $this->assertSame(<<<EOF
 ---------------------- -------------------------------------- 
  Label                  Value                                 
 ---------------------- -------------------------------------- 
  Version                1                                     
  Canonical (RFC 4122)   4c8e3a2a-5993-11eb-a861-2bf05af69e52  
  Base 58                ATJGVdrgFqvc6thDFXv1Qu                
  Base 32                2CHRX2MPCK27NTGR9BY1DFD7JJ            
 ---------------------- -------------------------------------- 
  Timestamp              2021-01-18 13:44:34.438609            
 ---------------------- -------------------------------------- 


EOF
            , $commandTester->getDisplay(true));
    }

    public function testV3()
    {
        $commandTester = new CommandTester(new InspectUuidCommand());

        $this->assertSame(0, $commandTester->execute(['uuid' => 'd108a1a0-957e-3c77-b110-d3f912374439']));
        $this->assertSame(<<<EOF
 ---------------------- -------------------------------------- 
  Label                  Value                                 
 ---------------------- -------------------------------------- 
  Version                3                                     
  Canonical (RFC 4122)   d108a1a0-957e-3c77-b110-d3f912374439  
  Base 58                Sp7q16VVeC7zPsMPVEToq2                
  Base 32                6H12GT15BY7HVV246KZ493EH1S            
 ---------------------- -------------------------------------- 


EOF
            , $commandTester->getDisplay(true));
    }

    public function testV4()
    {
        $commandTester = new CommandTester(new InspectUuidCommand());

        $this->assertSame(0, $commandTester->execute(['uuid' => '705c6eab-a535-4f49-bd51-436d0e81206a']));
        $this->assertSame(<<<EOF
 ---------------------- -------------------------------------- 
  Label                  Value                                 
 ---------------------- -------------------------------------- 
  Version                4                                     
  Canonical (RFC 4122)   705c6eab-a535-4f49-bd51-436d0e81206a  
  Base 58                EsjuVs1nd42xt7jSB8hNQH                
  Base 32                3GBHQAQ99N9X4VTMA3DM78283A            
 ---------------------- -------------------------------------- 


EOF
            , $commandTester->getDisplay(true));
    }

    public function testV5()
    {
        $commandTester = new CommandTester(new InspectUuidCommand());

        $this->assertSame(0, $commandTester->execute(['uuid' => '4ec6c3ad-de94-5f75-b5f0-ad56661a30c4']));
        $this->assertSame(<<<EOF
 ---------------------- -------------------------------------- 
  Label                  Value                                 
 ---------------------- -------------------------------------- 
  Version                5                                     
  Canonical (RFC 4122)   4ec6c3ad-de94-5f75-b5f0-ad56661a30c4  
  Base 58                AjCoyQeK6TtFemqYWV5uKZ                
  Base 32                2ERV1TVQMMBXTVBW5DASK1MC64            
 ---------------------- -------------------------------------- 


EOF
            , $commandTester->getDisplay(true));
    }

    public function testV6()
    {
        $commandTester = new CommandTester(new InspectUuidCommand());

        $this->assertSame(0, $commandTester->execute(['uuid' => '1eb59937-b0a7-6288-a861-db3dc2d8d4db']));
        $this->assertSame(<<<EOF
 ---------------------- -------------------------------------- 
  Label                  Value                                 
 ---------------------- -------------------------------------- 
  Version                6                                     
  Canonical (RFC 4122)   1eb59937-b0a7-6288-a861-db3dc2d8d4db  
  Base 58                4nwhs6vwvNU2AbcCSD1XP8                
  Base 32                0YPPCKFC57CA4AGREV7Q1DHN6V            
 ---------------------- -------------------------------------- 
  Timestamp              2021-01-18 13:45:52.427892            
 ---------------------- -------------------------------------- 


EOF
            , $commandTester->getDisplay(true));
    }
}
