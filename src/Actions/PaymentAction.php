<?php

declare(strict_types=1);

namespace PayPlugPluginCore\Actions;

use PayPlugPluginCore\Gateways\PaymentGatewayManager;
use PayPlugPluginCore\Gateways\RefundGateway;
use PayPlugPluginCore\Models\Entities\PaymentInputDTO;
use PayPlugPluginCore\Models\Entities\PaymentOutputDTO;
use PayPlugPluginCore\Models\Entities\RefundInputDTO;
use PayPlugPluginCore\Models\Entities\RefundOutputDTO;
use PayPlugPluginCore\Utilities\Services\Api;
use PayPlugPluginCore\Validators\PaymentResourceValidator;
use PayPlugPluginCore\Validators\RefundValidator;

class PaymentAction
{
    /**
     * @param PaymentInputDTO $payment_inputDTO
     *
     * @return ?PaymentOutputDTO
     * @throws \Exception
     */
    public function createAction(PaymentInputDTO $payment_inputDTO): ?PaymentOutputDTO
    {
        // todo: add a validator to check if the given paymentDTO is usable

        if (null === $payment_inputDTO->getPaymentMethod()) {
            throw new \Exception('Invalid parameter, payment method is required.');
        }

        // get payment method from given arg
        $payment_method = $this
            ->get_payment_gateway()
            ->load($payment_inputDTO->getPaymentMethod());

        // get payment tab appropriate or return error if need to
        $formated_attributes = $payment_method->formatPaymentAttributes($payment_inputDTO);

        // load api service with given then return the fallback
        $api = $this->get_api()->load((string) $payment_inputDTO->getApiBearer());
        $resource = $api->createPaymentResource($formated_attributes);

        return PaymentOutputDTO::create($resource);
    }

    /**
     * @description
     */
    public function abortAction(): void
    {
    }

    /**
     * @description
     */
    public function captureAction(): void
    {
    }

    /**
     * @param RefundInputDTO $refund_inputDTO
     *
     * @return ?RefundOutputDTO
     * @throws \Exception
     */
    public function refundAction(RefundInputDTO $refund_inputDTO): ?RefundOutputDTO
    {
        if (null === $refund_inputDTO->getResource()) {
            throw new \Exception('Invalid parameter, resource is required.');
        }

        // Validate the refund input DTO to ensure it contains coherent and allowed data
        (new RefundValidator())->validate($refund_inputDTO);

        // Then check if the resource contain in the DTO is a valid payment resource
        $resource = $refund_inputDTO->getResource();
        $validator = new PaymentResourceValidator();
        $validator->validate($resource);
        $validator->validateIsPaid($resource);
        $resource_id = $resource->id;

        // Format the attributes for the refund request
        $refund_gateway = $this->get_refund_gateway();
        $formated_attributes = $refund_gateway->formatRefundAttributes($refund_inputDTO);

        // load api service with given then return the fallback
        $api = $this->get_api()->load((string)$refund_inputDTO->getApiBearer());
        $refund = $api->refundPaymentResource($resource_id, $formated_attributes);

        return RefundOutputDTO::create($refund);
    }

    public function get_payment_gateway(): PaymentGatewayManager
    {
        return new PaymentGatewayManager();
    }

    public function get_refund_gateway(): RefundGateway
    {
        return new RefundGateway();
    }

    public function get_api(): Api
    {
        return new Api();
    }
}
