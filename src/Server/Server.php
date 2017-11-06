<?php
namespace Server;

/**
 * IAddressable interface
 */
interface IAddressable {
    public function setAddress(string $address);
    public function getAddress():string;
}

/**
 * IConnectable interface
 */
interface IConnectable {
    public function setMaxConnections(int $maxConnections);
    public function getMaxConnections():int;
}

/**
 * Server class. Constructor supports functional
 * options.
 */
class Server implements IAddressable, IConnectable
{
    /**
     * Server address
     *
     * @var string
     */
    private $address = "127.0.0.1:8080";

    /**
     * Maximum num connections
     *
     * @var integer
     */
    private $maxConnections = 3;

    /**
     * Constructor
     *
     * @param callable ...$options
     */
    public function __construct(callable ...$options)
    {
        if (count($options)>0) {
            foreach($options as $opt) {
                $opt($this);
            }
        }
    }

    /**
     * Sets the host address
     *
     * @param string $address
     * @return void
     */
    public function setAddress(string $address)
    {
        $this->address = $address;
    }

    /**
     * Gets the host address
     *
     * @return string
     */
    public function getAddress():string
    {
        return $this->address;
    }

    /**
     * Sets the max num connections
     *
     * @param int $maxConnections
     * @return void
     */
    public function setMaxConnections(int $maxConnections)
    {
        $this->maxConnections = $maxConnections;
    }

    /**
     * Gets the maxc num connections
     *
     * @return int
     */
    public function getMaxConnections():int
    {
        return $this->maxConnections;
    }
}

/**
 * Functional Option function for host address.
 * Works with anything that implements IAddressable.
 *
 * @param string $address Host address and port
 * @return callable
 */
function WithAddress(string $address):callable
{
    return function(IAddressable $server) use ($address) {
        $server->setAddress($address);
    };
}

/**
 * Functional Option function for max connections.
 * Works with anything that implements IConnectable.
 *
 * @param int $maxConnections
 * @return callable
 */
function WithMaxConnections(int $maxConnections):callable
{
    return function(IConnectable $server) use($maxConnections) {
        $server->setMaxConnections($maxConnections);
    };
}

/**
 * Functional Option function for JSON options.
 * Works with Server types.
 *
 * @param string $rawJSON
 * @return callable
 */
function FromJSON(string $rawJSON):callable
{
    return FromArray(json_decode($rawJSON, true));
}

/**
 * Functional Option function for array of options.
 * Works with Server types.
 *
 * @param array $options
 * @return callable
 */
function FromArray(array $options):callable
{
    return function(Server $server) use($options) {

        if(array_key_exists('address', $options)) {
            $server->setAddress($options['address']);
        }

        if(array_key_exists('maxConnections', $options)) {
            $server->setMaxConnections($options['maxConnections']);
        }
    };
}
