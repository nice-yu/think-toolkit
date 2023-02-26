<?php
declare(strict_types=1);
namespace NiceYu\Toolkit\Dto;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\SerializerBuilder;
use NiceYu\Toolkit\Annotation\Validator;
use NiceYu\Toolkit\Annotation\ValidatorGroup;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use think\exception\HttpException;

abstract class AbstractRequestDtoTransformer extends AbstractDtoTransformer
{
    /**
     * AbstractRequestDtoTransformer constructor.
     */
    public function __construct()
    {
        try {
            list($controller, $action) = explode('@',request()->rule()->getName());
            /** annotation parser */
            $reflection = new ReflectionClass(static::class);
            $annotation = new AnnotationReader();
            $validatorGroup = $annotation->getMethodAnnotation(
                new ReflectionMethod($controller, $action), ValidatorGroup::class
            );
        } catch (ReflectionException $e) {
            throw new HttpException(304,'Reflection error');
        }

        /** Take Validation Rules - Custom */
        $customize = $this->transArrayObjects($reflection->getMethods(ReflectionMethod::IS_PROTECTED),function($method){
            return [$method->getName(),$method];
        });

        /** Take out validation rules - tp */
        $regular = $this->transArrayObjects($reflection->getProperties(),function($property) use ($annotation){
            $annotation =   $annotation->getPropertyAnnotation($property,Validator::class);
            if (is_null($annotation)){
                return null;
            }
            return [$property->getName(),$annotation];
        });

        /** separation rules */
        $rule = array();
        $methods = array();
        $message = array();
        $customizeKeys = array_keys((array)$customize);
        foreach ($regular as $keys=>$item){
            /**
             * @var Validator $item
             * @var ValidatorGroup $validatorGroup
             */
            if (is_null($validatorGroup) || ($item->getScene() && in_array($validatorGroup->getName(),$item->getScene()))){
                /**
                 * Take out the rule name
                 * @var Validator $item
                 */
                if ($item->getRule()){
                    $rules = array();
                    foreach ($item->getRule() as $v){
                        if (in_array($v,$customizeKeys)){
                            $methods[] = $v;
                        } else {
                            $rules[] = $v;
                        }
                    }
                    $rule[$keys] = implode('|', $rules);
                }

                /**
                 * Remove message error words
                 */
                if ($item->getMessage()){
                    foreach ($item->getMessage() as $k=>$v){
                        $message["$keys.$k"] = $v;
                    }
                }
            }
        }

        /** verify - tp */
        if ($rule && $message){
            validate($rule,$message)->check(request()->param());
        }

        /** verify - customize */
        if ($methods){
            foreach ($methods as $method){
                $this->{$method}();
            }
        }

        /** Filter out redundant parameters */
        $serialize = SerializerBuilder::create()->build();
        $data = $serialize->serialize(request()->param(),'json');
        $dto =  $serialize->deserialize($data, static::class, 'json');
        foreach ($dto as $keys=>$item){
            $this->{$keys} = $item;
        }
    }
}