<?php

namespace Webkul\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Shetabit\Visitor\Visitor as BaseVisitor;
use Shetabit\Visitor\Exceptions\ResolverNotFoundException;
use Webkul\Core\Jobs\UpdateCreateVisitIndex;

class Visitor extends BaseVisitor
{
    /**
     * Override constructor to ensure ResolverNotFoundException is available.
     */
    public function __construct(Request $request, $config)
    {
        $this->request = $request;
        $this->config = $config;
        $this->except = $config['except'];
        $this->via($this->config['default'], $this->config['resolver']);
        $this->setVisitor($request->user());
    }

    /**
     * Create a visit log.
     *
     * @return void
     */
    public function visit(?Model $model = null)
    {
        if (! core()->getConfigData('general.general.visitor_options.enabled')) {
            return;
        }

        foreach ($this->except as $path) {
            if ($this->request->is($path)) {
                return;
            }
        }

        UpdateCreateVisitIndex::dispatch($model, $this->prepareLog());
    }

    /**
     * Retrieve request's url.
     */
    public function url(): string
    {
        return $this->request->url();
    }

    /**
     * Prepare log's data.
     *
     *
     * @throws \Exception
     */
    protected function prepareLog(): array
    {
        return array_merge(parent::prepareLog(), [
            'channel_id' => core()->getCurrentChannel()->id,
        ]);
    }

    /**
     * Returns logs.
     *
     * @return array
     */
    public function getLog()
    {
        return $this->prepareLog();
    }
}
