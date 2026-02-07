#![cfg_attr(windows, feature(abi_vectorcall))]

mod common;
mod data_frame;
mod data_type;
mod exception;
mod expression;
mod lazy_frame;
mod lazy_group_by;
mod series;

use ext_php_rs::prelude::*;

#[php_module]
pub fn get_module(module: ModuleBuilder) -> ModuleBuilder {
    module
        .class::<exception::PolarsException>()
        .class::<data_frame::PhpDataFrame>()
        .class::<series::PhpSeries>()
        .class::<expression::PolarsExpr>()
        .class::<data_type::PolarsDataType>()
        .class::<lazy_frame::PhpLazyFrame>()
        .class::<lazy_group_by::PhpLazyGroupBy>()
        .enumeration::<expression::PolarsClosedInterval>()
}