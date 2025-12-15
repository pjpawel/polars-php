#![cfg_attr(windows, feature(abi_vectorcall))]

mod data_frame;
mod exception;
mod data_type;
mod expression;
mod common;

use ext_php_rs::prelude::*;


#[php_module]
pub fn get_module(module: ModuleBuilder) -> ModuleBuilder {
    module
        .class::<data_frame::PhpDataFrame>()
        .class::<expression::PolarsExpr>()
        .class::<data_type::PolarsDataType>()
}