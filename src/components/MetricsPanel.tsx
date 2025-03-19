import React from 'react';
import {Metrics} from "@/commons/interfaces";
import Card from "@/components/Card";

function MetricsPanel({metrics, id}: { metrics: Metrics[], id?: string }) {
    return (
        <div id={id} className="space-y-4 md:space-y-0 w-full md:grid md:grid-cols-2 lg:flex lg:justify-around gap-4">
            {metrics.map((metric, index) => (
                <Card
                    key={index}
                    className="flex-col justify-center items-start gap-2 inline-flex w-full">
                    <div className="flex-col justify-start items-start flex">
                        <div
                            className="text-white text-base font-medium leading-normal">
                            {metric.title}
                        </div>
                        <div
                            className="text-stone-400 text-xs font-medium leading-tight">
                            {metric.subtitle}
                        </div>
                    </div>
                    <div
                        className="text-primary text-3xl font-light uppercase leading-10">
                        {metric.value}
                    </div>
                </Card>
            ))}
        </div>
    );
}

export default MetricsPanel;