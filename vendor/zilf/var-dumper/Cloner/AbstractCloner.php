<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zilf\VarDumper\Cloner;

use Zilf\VarDumper\Caster\Caster;
use Zilf\VarDumper\Exception\ThrowingCasterException;

/**
 * AbstractCloner implements a generic caster mechanism for objects and resources.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
abstract class AbstractCloner implements ClonerInterface
{
    public static $defaultCasters = array(
        'Zilf\VarDumper\Caster\CutStub' => 'Zilf\VarDumper\Caster\StubCaster::castStub',
        'Zilf\VarDumper\Caster\CutArrayStub' => 'Zilf\VarDumper\Caster\StubCaster::castCutArray',
        'Zilf\VarDumper\Caster\ConstStub' => 'Zilf\VarDumper\Caster\StubCaster::castStub',
        'Zilf\VarDumper\Caster\EnumStub' => 'Zilf\VarDumper\Caster\StubCaster::castEnum',

        'Closure' => 'Zilf\VarDumper\Caster\ReflectionCaster::castClosure',
        'Generator' => 'Zilf\VarDumper\Caster\ReflectionCaster::castGenerator',
        'ReflectionType' => 'Zilf\VarDumper\Caster\ReflectionCaster::castType',
        'ReflectionGenerator' => 'Zilf\VarDumper\Caster\ReflectionCaster::castReflectionGenerator',
        'ReflectionClass' => 'Zilf\VarDumper\Caster\ReflectionCaster::castClass',
        'ReflectionFunctionAbstract' => 'Zilf\VarDumper\Caster\ReflectionCaster::castFunctionAbstract',
        'ReflectionMethod' => 'Zilf\VarDumper\Caster\ReflectionCaster::castMethod',
        'ReflectionParameter' => 'Zilf\VarDumper\Caster\ReflectionCaster::castParameter',
        'ReflectionProperty' => 'Zilf\VarDumper\Caster\ReflectionCaster::castProperty',
        'ReflectionExtension' => 'Zilf\VarDumper\Caster\ReflectionCaster::castExtension',
        'ReflectionZendExtension' => 'Zilf\VarDumper\Caster\ReflectionCaster::castZendExtension',

        'Doctrine\Common\Persistence\ObjectManager' => 'Zilf\VarDumper\Caster\StubCaster::cutInternals',
        'Doctrine\Common\Proxy\Proxy' => 'Zilf\VarDumper\Caster\DoctrineCaster::castCommonProxy',
        'Doctrine\ORM\Proxy\Proxy' => 'Zilf\VarDumper\Caster\DoctrineCaster::castOrmProxy',
        'Doctrine\ORM\PersistentCollection' => 'Zilf\VarDumper\Caster\DoctrineCaster::castPersistentCollection',

        'DOMException' => 'Zilf\VarDumper\Caster\DOMCaster::castException',
        'DOMStringList' => 'Zilf\VarDumper\Caster\DOMCaster::castLength',
        'DOMNameList' => 'Zilf\VarDumper\Caster\DOMCaster::castLength',
        'DOMImplementation' => 'Zilf\VarDumper\Caster\DOMCaster::castImplementation',
        'DOMImplementationList' => 'Zilf\VarDumper\Caster\DOMCaster::castLength',
        'DOMNode' => 'Zilf\VarDumper\Caster\DOMCaster::castNode',
        'DOMNameSpaceNode' => 'Zilf\VarDumper\Caster\DOMCaster::castNameSpaceNode',
        'DOMDocument' => 'Zilf\VarDumper\Caster\DOMCaster::castDocument',
        'DOMNodeList' => 'Zilf\VarDumper\Caster\DOMCaster::castLength',
        'DOMNamedNodeMap' => 'Zilf\VarDumper\Caster\DOMCaster::castLength',
        'DOMCharacterData' => 'Zilf\VarDumper\Caster\DOMCaster::castCharacterData',
        'DOMAttr' => 'Zilf\VarDumper\Caster\DOMCaster::castAttr',
        'DOMElement' => 'Zilf\VarDumper\Caster\DOMCaster::castElement',
        'DOMText' => 'Zilf\VarDumper\Caster\DOMCaster::castText',
        'DOMTypeinfo' => 'Zilf\VarDumper\Caster\DOMCaster::castTypeinfo',
        'DOMDomError' => 'Zilf\VarDumper\Caster\DOMCaster::castDomError',
        'DOMLocator' => 'Zilf\VarDumper\Caster\DOMCaster::castLocator',
        'DOMDocumentType' => 'Zilf\VarDumper\Caster\DOMCaster::castDocumentType',
        'DOMNotation' => 'Zilf\VarDumper\Caster\DOMCaster::castNotation',
        'DOMEntity' => 'Zilf\VarDumper\Caster\DOMCaster::castEntity',
        'DOMProcessingInstruction' => 'Zilf\VarDumper\Caster\DOMCaster::castProcessingInstruction',
        'DOMXPath' => 'Zilf\VarDumper\Caster\DOMCaster::castXPath',

        'ErrorException' => 'Zilf\VarDumper\Caster\ExceptionCaster::castErrorException',
        'Exception' => 'Zilf\VarDumper\Caster\ExceptionCaster::castException',
        'Error' => 'Zilf\VarDumper\Caster\ExceptionCaster::castError',
        'Zilf\DependencyInjection\ContainerInterface' => 'Zilf\VarDumper\Caster\StubCaster::cutInternals',
        'Zilf\VarDumper\Exception\ThrowingCasterException' => 'Zilf\VarDumper\Caster\ExceptionCaster::castThrowingCasterException',
        'Zilf\VarDumper\Caster\TraceStub' => 'Zilf\VarDumper\Caster\ExceptionCaster::castTraceStub',
        'Zilf\VarDumper\Caster\FrameStub' => 'Zilf\VarDumper\Caster\ExceptionCaster::castFrameStub',

        'PHPUnit_Framework_MockObject_MockObject' => 'Zilf\VarDumper\Caster\StubCaster::cutInternals',
        'Prophecy\Prophecy\ProphecySubjectInterface' => 'Zilf\VarDumper\Caster\StubCaster::cutInternals',
        'Mockery\MockInterface' => 'Zilf\VarDumper\Caster\StubCaster::cutInternals',

        'PDO' => 'Zilf\VarDumper\Caster\PdoCaster::castPdo',
        'PDOStatement' => 'Zilf\VarDumper\Caster\PdoCaster::castPdoStatement',

        'AMQPConnection' => 'Zilf\VarDumper\Caster\AmqpCaster::castConnection',
        'AMQPChannel' => 'Zilf\VarDumper\Caster\AmqpCaster::castChannel',
        'AMQPQueue' => 'Zilf\VarDumper\Caster\AmqpCaster::castQueue',
        'AMQPExchange' => 'Zilf\VarDumper\Caster\AmqpCaster::castExchange',
        'AMQPEnvelope' => 'Zilf\VarDumper\Caster\AmqpCaster::castEnvelope',

        'ArrayObject' => 'Zilf\VarDumper\Caster\SplCaster::castArrayObject',
        'SplDoublyLinkedList' => 'Zilf\VarDumper\Caster\SplCaster::castDoublyLinkedList',
        'SplFileInfo' => 'Zilf\VarDumper\Caster\SplCaster::castFileInfo',
        'SplFileObject' => 'Zilf\VarDumper\Caster\SplCaster::castFileObject',
        'SplFixedArray' => 'Zilf\VarDumper\Caster\SplCaster::castFixedArray',
        'SplHeap' => 'Zilf\VarDumper\Caster\SplCaster::castHeap',
        'SplObjectStorage' => 'Zilf\VarDumper\Caster\SplCaster::castObjectStorage',
        'SplPriorityQueue' => 'Zilf\VarDumper\Caster\SplCaster::castHeap',
        'OuterIterator' => 'Zilf\VarDumper\Caster\SplCaster::castOuterIterator',

        'MongoCursorInterface' => 'Zilf\VarDumper\Caster\MongoCaster::castCursor',

        ':curl' => 'Zilf\VarDumper\Caster\ResourceCaster::castCurl',
        ':dba' => 'Zilf\VarDumper\Caster\ResourceCaster::castDba',
        ':dba persistent' => 'Zilf\VarDumper\Caster\ResourceCaster::castDba',
        ':gd' => 'Zilf\VarDumper\Caster\ResourceCaster::castGd',
        ':mysql link' => 'Zilf\VarDumper\Caster\ResourceCaster::castMysqlLink',
        ':pgsql large object' => 'Zilf\VarDumper\Caster\PgSqlCaster::castLargeObject',
        ':pgsql link' => 'Zilf\VarDumper\Caster\PgSqlCaster::castLink',
        ':pgsql link persistent' => 'Zilf\VarDumper\Caster\PgSqlCaster::castLink',
        ':pgsql result' => 'Zilf\VarDumper\Caster\PgSqlCaster::castResult',
        ':process' => 'Zilf\VarDumper\Caster\ResourceCaster::castProcess',
        ':stream' => 'Zilf\VarDumper\Caster\ResourceCaster::castStream',
        ':stream-context' => 'Zilf\VarDumper\Caster\ResourceCaster::castStreamContext',
        ':xml' => 'Zilf\VarDumper\Caster\XmlResourceCaster::castXml',
    );

    protected $maxItems = 2500;
    protected $maxString = -1;
    protected $useExt;

    private $casters = array();
    private $prevErrorHandler;
    private $classInfo = array();
    private $filter = 0;

    /**
     * @param callable[]|null $casters A map of casters
     *
     * @see addCasters
     */
    public function __construct(array $casters = null)
    {
        if (null === $casters) {
            $casters = static::$defaultCasters;
        }
        $this->addCasters($casters);
        $this->useExt = extension_loaded('symfony_debug');
    }

    /**
     * Adds casters for resources and objects.
     *
     * Maps resources or objects types to a callback.
     * Types are in the key, with a callable caster for value.
     * Resource types are to be prefixed with a `:`,
     * see e.g. static::$defaultCasters.
     *
     * @param callable[] $casters A map of casters
     */
    public function addCasters(array $casters)
    {
        foreach ($casters as $type => $callback) {
            $this->casters[strtolower($type)][] = $callback;
        }
    }

    /**
     * Sets the maximum number of items to clone past the first level in nested structures.
     *
     * @param int $maxItems
     */
    public function setMaxItems($maxItems)
    {
        $this->maxItems = (int) $maxItems;
    }

    /**
     * Sets the maximum cloned length for strings.
     *
     * @param int $maxString
     */
    public function setMaxString($maxString)
    {
        $this->maxString = (int) $maxString;
    }

    /**
     * Clones a PHP variable.
     *
     * @param mixed $var    Any PHP variable
     * @param int   $filter A bit field of Caster::EXCLUDE_* constants
     *
     * @return Data The cloned variable represented by a Data object
     */
    public function cloneVar($var, $filter = 0)
    {
        $this->prevErrorHandler = set_error_handler(function ($type, $msg, $file, $line, $context) {
            if (E_RECOVERABLE_ERROR === $type || E_USER_ERROR === $type) {
                // Cloner never dies
                throw new \ErrorException($msg, 0, $type, $file, $line);
            }

            if ($this->prevErrorHandler) {
                return call_user_func($this->prevErrorHandler, $type, $msg, $file, $line, $context);
            }

            return false;
        });
        $this->filter = $filter;

        try {
            $data = $this->doClone($var);
        } catch (\Exception $e) {
        }
        restore_error_handler();
        $this->prevErrorHandler = null;

        if (isset($e)) {
            throw $e;
        }

        return new Data($data);
    }

    /**
     * Effectively clones the PHP variable.
     *
     * @param mixed $var Any PHP variable
     *
     * @return array The cloned variable represented in an array
     */
    abstract protected function doClone($var);

    /**
     * Casts an object to an array representation.
     *
     * @param Stub $stub     The Stub for the casted object
     * @param bool $isNested True if the object is nested in the dumped structure
     *
     * @return array The object casted as array
     */
    protected function castObject(Stub $stub, $isNested)
    {
        $obj = $stub->value;
        $class = $stub->class;

        if (isset($class[15]) && "\0" === $class[15] && 0 === strpos($class, "class@anonymous\x00")) {
            $stub->class = get_parent_class($class).'@anonymous';
        }
        if (isset($this->classInfo[$class])) {
            $classInfo = $this->classInfo[$class];
        } else {
            $classInfo = array(
                new \ReflectionClass($class),
                array_reverse(array($class => $class) + class_parents($class) + class_implements($class) + array('*' => '*')),
            );

            $this->classInfo[$class] = $classInfo;
        }

        $a = $this->callCaster('Zilf\VarDumper\Caster\Caster::castObject', $obj, $classInfo[0], null, $isNested);

        foreach ($classInfo[1] as $p) {
            if (!empty($this->casters[$p = strtolower($p)])) {
                foreach ($this->casters[$p] as $p) {
                    $a = $this->callCaster($p, $obj, $a, $stub, $isNested);
                }
            }
        }

        return $a;
    }

    /**
     * Casts a resource to an array representation.
     *
     * @param Stub $stub     The Stub for the casted resource
     * @param bool $isNested True if the object is nested in the dumped structure
     *
     * @return array The resource casted as array
     */
    protected function castResource(Stub $stub, $isNested)
    {
        $a = array();
        $res = $stub->value;
        $type = $stub->class;

        if (!empty($this->casters[':'.$type])) {
            foreach ($this->casters[':'.$type] as $c) {
                $a = $this->callCaster($c, $res, $a, $stub, $isNested);
            }
        }

        return $a;
    }

    /**
     * Calls a custom caster.
     *
     * @param callable        $callback The caster
     * @param object|resource $obj      The object/resource being casted
     * @param array           $a        The result of the previous cast for chained casters
     * @param Stub            $stub     The Stub for the casted object/resource
     * @param bool            $isNested True if $obj is nested in the dumped structure
     *
     * @return array The casted object/resource
     */
    private function callCaster($callback, $obj, $a, $stub, $isNested)
    {
        try {
            $cast = call_user_func($callback, $obj, $a, $stub, $isNested, $this->filter);

            if (is_array($cast)) {
                $a = $cast;
            }
        } catch (\Exception $e) {
            $a[(Stub::TYPE_OBJECT === $stub->type ? Caster::PREFIX_VIRTUAL : '').'⚠'] = new ThrowingCasterException($e);
        }

        return $a;
    }
}
