<?php

namespace PayplugPluginCore\Actions;

use PayplugPluginCore\Models\Entities\PaymentInputDTO;
use PayplugPluginCore\Models\Entities\PaymentOutputDTO;
use PayplugPluginCore\Utilities\Traits\DependenciesLoader;

class PaymentAction
{
    use dependenciesLoader;

    /**
     * @param PaymentInputDTO $payment_inputDTO
     *
     * @throws \Exception
     *
     * @return PaymentOutputDTO
     */
    public function createAction(PaymentInputDTO $payment_inputDTO): object
    {
        // todo: add a validator to check if the given paymentDTO is usable

        // get payment method from given arg
        $payment_method = $this
            ->get_gateway('payment')
            ->load($payment_inputDTO->getPaymentMethod());

        // get payment tab appropriate or return error if need to
        $formated_attributes = $payment_method->formatPaymentAttributes($payment_inputDTO);

        // load api service with given then return the fallback
        $api = $this
            ->get_service('api')
            ->load((string) $payment_inputDTO->getApiBearer());
        $resource = $api->createPaymentResource($formated_attributes);

        return PaymentOutputDTO::create($resource);
    }

    /**
     * @description
     */
    public function abortAction()
    {
    }

    /**
     * @description
     */
    public function captureAction()
    {
    }

    /**
     * @description
     */
    public function refundAction()
    {
    }
}
