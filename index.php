<?php


interface DtoInterface
{

}

abstract class AbstractDto
{
    /**
     * AbstractRequestDto constructor.
     * @param object $data
     */
    public function __construct(object $data)
    {
        if (!$this->map($data)) {
            throw new InvalidArgumentException('Maper не удался');
        }
    }

    /* @return array */
    abstract protected function configureValidatorRules(): array;

    /**
     * @param object $data
     * @return bool
     */
    abstract protected function map(object $data): bool;
}

class ExchangeLatestDto extends AbstractDto implements DtoInterface
{

    public $disclaimer;
    public $license;
    public $timestamp;
    public $base;
    public $rates;

    /* @return array */
    protected function configureValidatorRules(): array
    {
        return [
            'disclaimer' => 'required',
            'license' => 'required',
            'timestamp' => 'required',
            'base' => 'required',
            'rates' => 'required',
        ];
    }

    /**
     * @inheritDoc
     */
    protected function map(object $data): bool
    {
        $this->disclaimer = $data->disclaimer;
        $this->license = $data->license;
        $this->timestamp = $data->timestamp;
        $this->base = $data->base;
        $this->rates = $data->rates;
        return true;
    }
}

interface ServiceInterface
{

    /**
     * @param DtoInterface $dto
     * @return ServiceInterface
     */
    public static function send(DtoInterface $dto): object;
}

class ExchangeLatestService implements ServiceInterface{
    /**
     * @param DtoInterface $dto
     * @return ServiceInterface
     */
    public static function send(DtoInterface $dto): object
    {

        if (!$dto instanceof ExchangeLatestDto) {
            throw new InvalidArgumentException('ExchangeLatestService необходимо для отправки в ExchangeLatestDto.');
        }

        /* @var ExchangeLatestDto $dto */
        return $dto;
    }

}

class ExchangeLatestHttp {
    /**
     * @return object
     */
    public static function request() : object
    {
        $ch = curl_init('https://openexchangerates.org/api/latest.json?app_id=c156ea2b4f5f4bfeba1162bc7814f28f');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return json_decode($data);
    }
}

$data = ExchangeLatestHttp::request();
$exchangeLatestDto = new ExchangeLatestDto($data);


return print_r(ExchangeLatestService::send($exchangeLatestDto));
